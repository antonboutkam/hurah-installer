<?php
namespace Hi\Installer\Db;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Script\Event;
use Hi\Helpers\Console;
use Hi\Helpers\DirectoryStructure;
use Hi\Installer\AbstractInstaller;

class Installer
{
    const logTopic = "Novum environment setup";

    public function install(Event $oEvent)
    {

        $oConsole = new Console($oEvent->getIO());

        $oDirectoryStructure = new DirectoryStructure();

        $oConsole->log("Creating env directory", self::logTopic);
        $this->createEnvRoot($oDirectoryStructure, $oConsole);

        $oConsole->log("Creating domain env files", self::logTopic);
        $this->createEnvFiles($oDirectoryStructure, $oConsole);
    }

    function createEnvRoot(DirectoryStructure $oDirectoryStructure, Console $oConsole)
    {
        $bEnvDirExists = is_dir($oDirectoryStructure->getEnvDir());

        if(!$bEnvDirExists)
        {
            $oConsole->log("Creating environment directory: " . $oDirectoryStructure->getEnvDir(), self::logTopic);
            mkdir($oDirectoryStructure->getEnvDir());
        }
        else
        {
            $oConsole->log("No need to create environment directory as it already exists: " . $oDirectoryStructure->getEnvDir(), self::logTopic);
        }
    }

    function createEnvFiles(DirectoryStructure $oDirectoryStructure, Console $oConsole)
    {
        foreach ($oDirectoryStructure->getDomainCollection() as $oDomain)
        {
            $oConsole->log("Creating environment for " . $oDomain->getSystemID());


            if(file_exists('.env'))
            {
                // When installation is done trough the Docker instaler
                $aDockerEnvironment = parse_ini_file('.env');
                $sEnvFileUserPerspective = $oDomain->getDirectory()->getPathname() . DIRECTORY_SEPARATOR . '.env';
                $sDbIp = $aDockerEnvironment['DATABASE_IP'];
            }
            else
            {
                // For now assuming localhost
                $sDbIp = '127.0.0.1';
            }

            $sEnvFileSystemPerspective = $oDirectoryStructure->getSystemDir() . '/env/.' . $oDomain->getSystemID();

            if(!file_exists($sEnvFileUserPerspective))
            {
                $oEnvFile = new EnvFile(
                    $oDomain->getSystemID(),
                    $oDomain->getSystemRoot(),
                    $oDomain->getDataDir(),
                    $sDbIp,
                    $oDomain->makeDbUser(),
                    $oDomain->makeDbPass());

                $oConsole->log("Creating new environment configuration " . $sEnvFileUserPerspective, self::logTopic);
                file_put_contents($sEnvFileUserPerspective, $oEnvFile);
            }
            else
            {
                $oConsole->log("Environment configuration already exists for " . $oDomain->getSystemID(), self::logTopic);
            }

            $oConsole->log("Symlinking  $sEnvFileUserPerspective => $sEnvFileSystemPerspective", self::logTopic);
            symlink($sEnvFileUserPerspective, $sEnvFileSystemPerspective);
        }
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
      //  Util::removeSymlink($this->getVirtualInstallPath($package));
    }

}
