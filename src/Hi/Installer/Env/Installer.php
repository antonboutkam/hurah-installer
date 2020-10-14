<?php
namespace Hi\Installer\Env;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Script\Event;
use Hi\Helpers\Console;
use Hi\Helpers\DirectoryStructure;
use Hi\Installer\AbstractInstaller;
use Hi\Installer\Util;

class Installer
{
    const logTopic = "Novum environment setup";

    public function install(Event $oEvent) {
        $oConsole = new Console($oEvent->getIO());
        $oDirectoryStructure = new DirectoryStructure();
        $oConsole->log("Creating domain env files", self::logTopic);
        $this->createEnvFiles($oDirectoryStructure, $oConsole);
    }

    function createEnvFiles(DirectoryStructure $oDirectoryStructure, Console $oConsole)
    {
        foreach ($oDirectoryStructure->getDomainCollection() as $oDomain)
        {
            $oConsole->log("Creating environment for " . $oDomain->getSystemID(), self::logTopic);

            if(file_exists('.env'))
            {
                // When installation is done trough the Docker instaler
                $aDockerEnvironment = parse_ini_file('.env');
                $sDbIp = $aDockerEnvironment['DATABASE_IP'];
            }
            else
            {
                // For now assuming localhost
                $sDbIp = '127.0.0.1';
            }

            $sEnvFileUserPerspective = $oDomain->getPathname() . DIRECTORY_SEPARATOR . '.env';
            $sEnvFileSystemPerspective = $oDirectoryStructure->getSystemDir() . '/env/.' . $oDomain->getSystemID();

            if(!file_exists($sEnvFileUserPerspective))
            {
                $oEnvFile = new EnvFile(
                    $oDomain->getSystemID(),
                    $oDomain->getSystemRoot(),
                    $oDomain->getSystemRoot() . DIRECTORY_SEPARATOR . $oDomain->getDataDir(),
                    $sDbIp,
                    $oDomain->makeDbUser(),
                    $oDomain->makeDbPass());

                $oConsole->log("Creating new environment configuration " . $sEnvFileUserPerspective, self::logTopic);
                file_put_contents($sEnvFileUserPerspective, $oEnvFile);
            }
            else
            {
                $oConsole->log("Environment configuration already exists for  {$oDomain->getSystemID()} at $sEnvFileUserPerspective", self::logTopic);
            }

            $sEnvDir = dirname($sEnvFileSystemPerspective);
            if(!is_dir($sEnvDir))
            {
                $oConsole->log("Creating env directory {$sEnvDir}", self::logTopic);
                mkdir($sEnvDir, 0777, true);
            }

            $sRelativePath = str_repeat('..' . DIRECTORY_SEPARATOR, 4);
            $oConsole->log("Symlinking  {$sRelativePath}{$sEnvFileUserPerspective} => $sEnvFileSystemPerspective", self::logTopic);

            Util::removeSymlink($sEnvFileSystemPerspective);
            symlink($sRelativePath . $sEnvFileUserPerspective, $sEnvFileSystemPerspective);
        }

        $oDirectoryStructure->getLogDir();

    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
      //  Util::removeSymlink($this->getVirtualInstallPath($package));
    }

}
