<?php
namespace Hi\Installer\Core;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Installer\AbstractInstaller;
use Hi\Installer\Util;

final class Installer extends AbstractInstaller implements InstallerInterface
{
    private function loadConstants(PackageInterface $package):void
    {
        define('INSTALL_DIR', $this->getVirtualInstallPath($package));
        define('PREV_INSTALL_DIR', INSTALL_DIR . '.prev');
        define('INSTALL_DIR_TEMP', INSTALL_DIR . '.new');
    }
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->io->write("Attempting to install Novum core system");
        parent::install($repo, $package);

        $this->loadConstants($package);

        $this->io->write("Create intermediate location for building.");
        $this->io->write("Symlink to: " . INSTALL_DIR_TEMP);
        symlink($this->getInstallPath($package), INSTALL_DIR_TEMP);

        if(file_exists(PREV_INSTALL_DIR))
        {
            $this->io->write("Cleaning up previous backup of install dir.");
            Util::removeSymlink(PREV_INSTALL_DIR);
        }
        if(file_exists(INSTALL_DIR))
        {
            $this->io->write("Copy current installation to temporary location.");
            rename(INSTALL_DIR, PREV_INSTALL_DIR);
        }

        $this->io->write("Putting new core system in place");
        rename(INSTALL_DIR_TEMP, INSTALL_DIR);

        $this->io->write("Core system installed");

        sleep(4);
    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->loadConstants($package);
        $this->io->write("Cleaning up symlinks from Novum Core system");
        file_exists(INSTALL_DIR_TEMP) ? $this->removeSymlink(INSTALL_DIR) : null;
        file_exists(PREV_INSTALL_DIR) ? $this->removeSymlink(PREV_INSTALL_DIR) : null;
        file_exists(INSTALL_DIR_TEMP) ? $this->removeSymlink(INSTALL_DIR_TEMP) : null;
        $this->io->write("All done");
    }
    private function getVirtualInstallPath(PackageInterface $package):string
    {
        if(file_exists('system'))
        {
            $this->io->writeError("Could not create system install dir");
        }
        return 'system';
    }
    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-core' === $packageType;
    }
}
