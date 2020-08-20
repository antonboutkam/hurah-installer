<?php
namespace Hi\Installer\Domain;

use Composer\Package\PackageInterface;
use Composer\Installer\InstallerInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Dotenv\Dotenv;
use Exception;
use Hi\Installer\AbstractInstaller;
use Hi\Installer\Domain\Component\Database;
use Hi\Installer\Domain\Component\FileStructure;

class Installer extends AbstractInstaller implements InstallerInterface
{
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);

        try
        {
            $oFileStructure = new FileStructure();
            $oFileStructure->install($_SERVER['SYSTEM_ID'], $this->getInstallPath($package), $this->io);
        }
        catch (Exception $e)
        {
            $this->io->write("Cannot create file structure");
        }

        try
        {
            $oDotenv = Dotenv::createImmutable($this->getInstallPath($package));
            $oDotenv->load();

            $oDatabase = new Database();
            $oDatabase->install($_SERVER, $this->getInstallPath($package), $this->io);
        }
        catch (Exception $e)
        {
            $this->io->write("Cannot create database, .env file is missing");
        }


    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $oDotenv = Dotenv::createImmutable($this->getInstallPath($package));
        $oDotenv->load();

        parent::uninstall($repo, $package);
        $oFileStructure = new FileStructure();
        $oFileStructure->uninstall($_SERVER['SYSTEM_ID'], $this->getInstallPath($package), $this->io);
    }


    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-domain' === $packageType || 'hurah-domain' === $packageType ;
    }
}
