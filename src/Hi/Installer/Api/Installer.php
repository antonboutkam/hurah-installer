<?php
namespace Hi\Installer\Api;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Installer\AbstractInstaller;
use Hi\Installer\Util;

final class Installer extends AbstractInstaller implements InstallerInterface
{
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
        $this->io->write("Symlinking " . $this->getInstallPath($package) . ' => '. $this->getVirtualInstallPath($package));
        symlink($this->getInstallPath($package), $this->getVirtualInstallPath($package));
    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        Util::removeSymlink($this->getVirtualInstallPath($package));
    }
    private function getVirtualInstallPath(PackageInterface $package):string
    {
        $sDirName = './system';
        if(!is_dir($sDirName))
        {
            mkdir($sDirName, 0777, true);
        }
        return $sDirName . '/' . substr($package->getPrettyName(), 10);
    }


    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-api' === $packageType || 'hurah-api' === $packageType ;
    }
}
