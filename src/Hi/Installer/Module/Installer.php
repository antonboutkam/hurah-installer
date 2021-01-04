<?php
namespace Hi\Installer\Module;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Helpers\DirectoryStructure;
use Hi\Installer\AbstractInstaller;
use Hi\Installer\Util;
use Hi\Helpers\StructureCreator;
use InvalidArgumentException;

class Installer extends AbstractInstaller implements InstallerInterface
{

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        StructureCreator::create(new DirectoryStructure(), $this->io);
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
    public function getInstallPath(PackageInterface $package) :string
    {
        $prefix = substr($package->getPrettyName(), 0, 10);
        if ('novum-module-' !== $prefix && 'hurah-module-' !== $prefix ) {
            throw new InvalidArgumentException(
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
