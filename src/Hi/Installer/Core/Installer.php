<?php
namespace Hi\Installer\Core;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Installer\AbstractInstaller;
use Hi\Helpers\Console;
use Hi\Helpers\DirectoryStructure;
use Hi\Helpers\StructureCreator;
use Hi\Installer\Util;
use React\Promise\PromiseInterface;

final class Installer extends AbstractInstaller implements InstallerInterface
{
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $postInstaller = function() use ($package) {
            $oConsole = new Console($this->io);
            $oDirectoryStructure = new DirectoryStructure();

            /**
             * Installing the core files into the vendor folder
             */
            $oConsole->log("Installing core system", 'Novum core installer');
            StructureCreator::create($oDirectoryStructure, $this->io);
            $oConsole->log("Downloading dependencies", 'Novum core installer');

            /**
             * Symlinking to the system folder
             */
            $oConsole->log('Symlinking ' . $this->getRelativeInstallPath($package) . ' => ' .  $oDirectoryStructure->getSystemDir(), 'Novum core installer');

            symlink($this->getRelativeInstallPath($package), $oDirectoryStructure->getSystemDir());
            $oConsole->log("All done", 'Novum core installer');
        };


        $promise = parent::install($repo, $package);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($postInstaller);
        }

        // If not, execute the code right away as parent::uninstall executed synchronously (composer v1, or v2 without async)
        return $postInstaller();
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        Util::removeSymlink($this->getRelativeInstallPath($package));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-core' === $packageType;
    }
}
