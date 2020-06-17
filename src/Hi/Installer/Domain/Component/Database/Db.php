<?php
namespace Hi\Installer\Domain\Component\Database;

use Composer\IO\IOInterface;
use mysqli;

class Db
{
    static function create(array $aConnectProps, IOInterface $io):bool
    {
        // 1. Connect with normal params.
        //   1.1 Check if signing in with normal params is possible.
        //   1.2 Check if database exists.
        //      if 1.1 and not 1.2, must create database
        //      try create database
        //      if fail goto step 2.
        //   if 1.1 + 1.2 all is ok, script done
        //
        // 2. Check if ROOT user is available from environment file.
        //   if YES goto step 3
        //   if NO ask for ROOT credentials
        //
        // 3. Create stuff
        //    3.2 CREATE DATABASE
        //    3.2 CREATE USER

        if(self::canConnectWithNormalParams($aConnectProps))
        {
            $io->write("Connected to database.");
            $io->write("Now checking if the database {$aConnectProps['DB_NAME']} exists.");
            if(self::databaseExists($aConnectProps))
            {
                $io->write("Database exists, setting up is already done.");
                return true;
            }
        }
        else
        {
            $io->write("Could not establish a database connection with the provided params.");
            $io->write("Assuming the user still needs to be created.");
        }

        if(self::canConnectWithNormalParams($aConnectProps)
            && !self::databaseExists($aConnectProps)
            && !self::envFileContainsRootLogin($aConnectProps))
        {
            $bDatabaseCreated = self::tryCreateDb($aConnectProps, $io);

            if($bDatabaseCreated)
            {
                self::giveUserPermissions($aConnectProps, $io);
                return true;
            }
        }

        if(self::envFileContainsRootLogin($aConnectProps))
        {
            $aRootLogin = $aConnectProps;
        }
        else
        {
            $aRootLogin = self::askRootLogin($aConnectProps, $io);
        }

        self::createDatabaseWithRootLoginIfNotExists($aRootLogin);
        self::createUserIfNotExists($aRootLogin);

        return true;
    }
    static function createUserIfNotExists(array $aProps, IOInterface $io):bool
    {
        $io->write("Creating mysql user");
        $oMysqlI = new mysqli($aProps['DB_SERVER'], $aProps['ROOT_DB_USER'], $aProps['ROOT_DB_PASS']);
        self::grandAll($oMysqlI, $aProps, $io);
        return true;
    }
    static function createDatabaseWithRootLoginIfNotExists(array $aProps, IOInterface $io):bool
    {
        $oMysqlI = new mysqli($aProps['DB_SERVER'], $aProps['ROOT_DB_USER'], $aProps['ROOT_DB_PASS']);
        return self::createDbQuery($oMysqlI, $aProps['DB_NAME'], $io);
    }
    private static function giveUserPermissions(array $aProps, IOInterface $io)
    {
        $oMysqlI = new mysqli($aProps['DB_SERVER'], $aProps['DB_USER'], $aProps['DB_PASS']);
        self::grandAll($oMysqlI, $aProps, $io);
    }
    private static function grandAll(mysqli $oMysqlI, $aProps, IOInterface $io)
    {
        $io->write("Creating user");
        $oMysqlI->query("CREATE USER IF NOT EXISTS {$aProps['DB_USER']}.localhost IDENTIFIED BY '{$aProps['DB_PASS']};");
        $io->write("Grant privileges to user");
        $oMysqlI->query("GRANT ALL PRIVILEGES ON {$aProps['DB_USER']}.* TO '{$aProps['DB_USER']}'@'{$aProps['DB_HOST']}';");
    }
    private static function createDbQuery(mysqli $oMysqlI, array $sDbName, IOInterface $io):bool
    {
        $io->write("Create database $sDbName if not exists");
        if($oMysqlI->query('CREATE DATABASE IF NOT EXISTS ' . $sDbName) === true)
        {
            $io->write("Created");
            return true;
        }
        $io->write("Could not create");
        return false;
    }
    private static final function tryCreateDb(array $aProps, IOInterface $io):bool
    {
        $io->write("Attempting to create the database " . $aProps['DB_NAME']);
        $oMysqlI = new mysqli($aProps['DB_SERVER'], $aProps['DB_USER'], $aProps['DB_PASS']);
        if(!$oMysqlI->select_db($aProps['DB_NAME']))
        {
            if(self::createDbQuery($oMysqlI, $aProps['DB_NAME'], $io))
            {
                return true;
            }
        }
        return false;
    }
    private static final function askRootLogin(array $aConnectProps, IOInterface $io):array
    {
        $io->write("We need a mysql account with CREATE DATABASE and CREATE USER privileges to create the " .
            " database and/or create the user that you specified in your .env file");

        while (true)
        {

            $aConnectProps['ROOT_DB_USER'] = $io->ask("Mysql username: ", "root");
            $aConnectProps['ROOT_DB_PASS'] = $io->askAndHideAnswer("Mysql password: ");

            if(new mysqli($_SERVER['DB_HOST'], $_SERVER['ROOT_DB_USER'], $_SERVER['ROOT_DB_PASS']))
            {
                return $aConnectProps;
            }
            $io->writeError("Cannot sign in with the credentials specified, please try again.");
        }
        return $aConnectProps;
    }

    private static final function envFileContainsRootLogin(array $aConnectProps):bool
    {
        return isset($aConnectProps['ROOT_DB_USER']) && isset($aConnectProps['ROOT_DB_PASS']);
    }
    private static final function databaseExists(array $aProps):bool
    {
        $oMysqlI = new mysqli($aProps['DB_SERVER'], $aProps['DB_USER'], $aProps['DB_PASS']);
        return $oMysqlI->select_db($aProps['DB_NAME']);
    }
    private static final function canConnectWithNormalParams(array $aProps):bool
    {
        return (new mysqli($aProps['DB_SERVER'], $aProps['DB_USER'], $aProps['DB_PASS']) !== false);

    }

}

