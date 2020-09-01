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
    protected $installerName = 'Novum site installer';
    protected $unInstallerName = 'Novum site uninstall';

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $sSiteDir = $package->getExtra()['install_dir'];
        $oDirectoryStructure = new DirectoryStructure();
        $oConsole = new Console($this->io);

        $oConsole->log('Creating base directory structure', $this->installerName);

        /**
         * Creating the needed root directories
         */
        StructureCreator::create($oDirectoryStructure, $this->io);

        /**
         * Downloading and installing the required files into the vendor folder
         */
        parent::install($repo, $package);


        $oConsole->log('Symlinking ' . $this->getInstallPath($package, 2) . ' => ' . $oDirectoryStructure->getPublicSitePath($sSiteDir), $this->installerName);
        symlink($this->getInstallPath($package), $oDirectoryStructure->getPublicSitePath($sSiteDir));

        /**
         * Symlinking into system folder
         */
        $oConsole->log('Symlinking ' . $this->getInstallPath($package, 2) . ' => ' . $oDirectoryStructure->getSystemSitePath($sSiteDir), $this->installerName);
        symlink($this->getInstallPath($package), $oDirectoryStructure->getSystemSitePath($sSiteDir));

        /**
         * Symlinking into public folder
         */
        $sSitesDir = $oDirectoryStructure->getPublicSitePath($sSiteDir, 1);
        if(!is_dir(dirname($sSitesDir)))
        {
            mkdir(dirname($sSitesDir), 0777, true);
        }

        $oConsole->log('Site installation completed', $this->installerName);

    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $sSiteDir = $package->getExtra()['install_dir'];
        $oDirectoryStructure = new DirectoryStructure();
        $oConsole = new Console($this->io);
        $oConsole->log('Uninstalling ' . $package->getName(), $this->unInstallerName);

        $oConsole->log('Removing ' . $oDirectoryStructure->getPublicSitePath($sSiteDir), $this->unInstallerName);
        Util::removeSymlink($oDirectoryStructure->getPublicSitePath($sSiteDir));
        $oConsole->log('Removing ' . $oDirectoryStructure->getSystemSitePath($sSiteDir), $this->unInstallerName);
        Util::removeSymlink($oDirectoryStructure->getSystemSitePath($sSiteDir));

        $oConsole->log('Removing sourcefiles of ' . $package->getName(), $this->unInstallerName);

        parent::uninstall($repo, $package);
    }

    public function supports($packageType)
    {
        return 'novum-site' === $packageType || 'hurah-site' === $packageType ;
    }
}
