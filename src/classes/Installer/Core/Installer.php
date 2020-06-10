<?php
namespace Hi\Installer\Core;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Installer\AbstractInstaller;

final class Installer extends AbstractInstaller implements InstallerInterface
{
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        symlink($this->getInstallPath($package), $this->getVirtualInstallPath($package));
    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->removeSymlink($this->getVirtualInstallPath($package));
    }
    private function getVirtualInstallPath(PackageInterface $package):string
    {
        return 'app';
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
