<?php
namespace Hi\Installer\Site;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Installer\AbstractInstaller;
use Hi\Helpers\Console;
use Hi\Helpers\DirectoryStructure;
use Hi\Helpers\StructureCreator;
use Hi\Installer\Util;

class Installer extends AbstractInstaller implements InstallerInterface
{

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $sSiteDir = $package->getExtra()['install_dir'];
        $oDirectoryStructure = new DirectoryStructure();
        $oConsole = new Console($this->io);

        /**
         * Creating the needed root directories
         */
        StructureCreator::create($oDirectoryStructure, $this->io);

        /**
         * Downloading and installing the required files into the vendor folder
         */
        parent::install($repo, $package);

        /**
         * Symlinking into public folder
         */
        $oConsole->log('Symlinking ' . $this->getInstallPath($package) . ' => ' . $oDirectoryStructure->getPublicSitePath($sSiteDir));
        symlink($this->getInstallPath($package), $oDirectoryStructure->getPublicSitePath($sSiteDir));

        /**
         * Symlinking into system folder
         */
        $oConsole->log('Symlinking ' . $this->getInstallPath($package) . ' => ' . $oDirectoryStructure->getSystemSitePath($sSiteDir));
        symlink($this->getInstallPath($package), $oDirectoryStructure->getSystemSitePath($sSiteDir));


    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $sSiteDir = $package->getExtra()['install_dir'];
        $oDirectoryStructure = new DirectoryStructure();
        Util::removeSymlink($oDirectoryStructure->getPublicSitePath($sSiteDir));
        Util::removeSymlink($oDirectoryStructure->getPublicSitePath($sSiteDir));
    }

    public function supports($packageType)
    {
        return 'novum-api' === $packageType || 'hurah-api' === $packageType ;
    }
}
