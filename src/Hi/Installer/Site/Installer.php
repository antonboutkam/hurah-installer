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

        /**
         * Symlinking into public folder
         */
        $iDirsUp = 1;

        $sRelativeInstallPath = $this->getRelativeInstallPath($package, $iDirsUp);


        $oConsole->log('Symlinking ' . $sRelativeInstallPath . ' => ' . $oDirectoryStructure->getPublicSitePath($sSiteDir), $this->installerName);

        if(file_exists($oDirectoryStructure->getPublicSitePath($sSiteDir)))
        {
            $oConsole->log('Unlinking ' . $oDirectoryStructure->getPublicSitePath($sSiteDir), $this->installerName);
            unlink($oDirectoryStructure->getPublicSitePath($sSiteDir));
        }

        symlink($sRelativeInstallPath, $oDirectoryStructure->getPublicSitePath($sSiteDir));


        /**
         * Symlinking into system folder
         */
        $iDirsUp = 2;

        $sAbsoluteInstallPath = parent::getInstallPath($package);
        $sPackageDir = basename($sAbsoluteInstallPath); //bv api-belastingdiest
        $sRelativeVirtualInstallPath = "../../$sPackageDir";
        $oConsole->log("Symlinking $sRelativeVirtualInstallPath" . ' => ' . $oDirectoryStructure->getSystemSitePath($sSiteDir), $this->installerName);

        if(is_link($oDirectoryStructure->getSystemSitePath($sSiteDir)))
        {
            unlink($oDirectoryStructure->getSystemSitePath($sSiteDir));
        }

        symlink($sRelativeVirtualInstallPath, $oDirectoryStructure->getSystemSitePath($sSiteDir));


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

        // parent::uninstall($repo, $package);
    }

    public function supports($packageType)
    {
        return 'novum-site' === $packageType || 'hurah-site' === $packageType ;
    }
}
