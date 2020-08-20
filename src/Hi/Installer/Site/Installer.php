<?php
namespace Hi\Installer\Site;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Installer\AbstractInstaller;
use Hi\Installer\Util;

class Installer extends AbstractInstaller implements InstallerInterface
{

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->io->write("Symlinking " . $this->getInstallPath($package) . ' => '. $this->getVirtualInstallPath($package));
        symlink($this->getInstallPath($package), $this->getVirtualInstallPath($package));
    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        Util::removeSymlink($this->getVirtualInstallPath($package));
    }
    private function getVirtualInstallPath(PackageInterface $package):string
    {
        return 'system/public_html/'.substr($package->getPrettyName(), 10);
    }

    public function supports($packageType)
    {
        return 'novum-api' === $packageType || 'hurah-api' === $packageType ;
    }
}
