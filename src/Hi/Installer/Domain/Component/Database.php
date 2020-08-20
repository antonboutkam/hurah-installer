<?php
namespace Hi\Installer\Domain\Component;

use Composer\IO\IOInterface;
use Core\QueryMapper;
use Hi\Exceptions\InstallationException;
use Hi\Installer\Domain\Component\Database\Db;
use \mysqli;

class Database
{
    /**
     * @param string $sDomainConfigFolder
     * @param string $sOriginalInstallPath
     * @param IOInterface $io
     * @throws InstallationException
     */
    public function install(array $aDatabaseProps, string $sOriginalInstallPath, IOInterface $io)
    {
        try
        {
            $io->write(sprintf(" -  Novum domain installer <info>%s</info> ", "database availability"));
            $oDb = new Db();
            $oDb->create($aDatabaseProps, $io);

        }
        catch (InstallationException $e)
        {
            $io->writeError($e->getMessage());
        }


        $io->write(json_encode($aDatabaseProps));

    }
    public function uninstall(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io)
    {
        $io->write(" -  Novum uninstaller <warning>%s</warning>", "Not removing database, you need to do this manually.");


        $io->write("");
    }
}
