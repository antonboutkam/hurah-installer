<?php
namespace Hi\Installer\Module;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Helpers\AbstractInstaller;
use Hi\Helpers\StructureCreator;
use Hi\Helpers\Util;

class Installer extends AbstractInstaller implements InstallerInterface
{

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        StructureCreator::create();
        symlink($this->getRelativeInstallPath($package), $this->getVirtualInstallPath($package));
    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        Util::removeSymlink($this->getVirtualInstallPath($package));
    }
    private function getVirtualInstallPath(PackageInterface $package):string
    {
        return 'system/admin_modules/' . ucfirst(substr($package->getPrettyName(), 13));
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $prefix = substr($package->getPrettyName(), 0, 10);
        if ('novum-module-' !== $prefix && 'hurah-module-' !== $prefix ) {
            throw new \InvalidArgumentException(
                'Unable to install module, Modules '
                .'should always start their name with '
                .'"novum-module-" or "hurah-module-"'
            );
        }
        return parent::getInstallPath($package);
    }
    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-module' === $packageType || 'hurah-module' === $packageType ;
    }
}
