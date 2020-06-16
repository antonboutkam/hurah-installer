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

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $prefix = substr($package->getPrettyName(), 0, 10);
        if ('novum-api-' !== $prefix && 'hurah-api-' !== $prefix ) {
            throw new \InvalidArgumentException(
                'Unable to install template, Novum Api templates '
                .'should always start their package name with '
                .'"novum-api-" or "hurah-api-"'
            );
        }
        return parent::getInstallPath($package);
    }
    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-api' === $packageType || 'hurah-api' === $packageType ;
    }
}
