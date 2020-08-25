<?php
namespace Hi\Installer\Domain;

use Composer\Package\PackageInterface;
use Composer\Installer\InstallerInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Helpers\StructureCreator;
use Hi\Installer\AbstractInstaller;
use Hi\Helpers\Console;
use Hi\Helpers\DirectoryStructure;
use Hi\Installer\Util;
use phpDocumentor\Reflection\Utils;

class Installer extends AbstractInstaller implements InstallerInterface
{
    /**
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface $package
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $sSystemId = str_replace('/', '.', str_replace('-domain', '', $package->getName()));
        /**
         * Installing all files on the normal location inside vendor
         */
        parent::install($repo, $package);
        $oConsole = new Console($this->io);
        $oDirectoryStructure = new DirectoryStructure();
        StructureCreator::create($oDirectoryStructure, $this->io);

        /**
         * Generating a namespace
         */
        list($sOrg, $sDomain) = explode('.', str_replace('domain-', '', $sSystemId));
        $sDomainNsPart = preg_replace("/[^A-Za-z0-9 ]/", '_', $sDomain);
        $sNamespace = ucfirst($sOrg).ucfirst($sDomainNsPart);

        $oConsole->log("Generated namespace based on package name $sSystemId -> $sNamespace");


        /**
         * Create required directories
         */
        $aMapping = $oDirectoryStructure->getDomainSystemSymlinkMapping($sSystemId, $sNamespace);

        foreach ($aMapping as $sFrom => $sTo)
        {
            $oConsole->log('Symlinking ' . $this->getInstallPath($package) . '/' . $sFrom . ' => ' . $sTo);
            symlink($this->getInstallPath($package) . '/' . $sFrom, $sTo);
        }

        /**
         * Create public symlink
         */
        symlink($this->getInstallPath($package) . '/' . $sFrom, $sTo);

    }
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {

    }


    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-domain' === $packageType || 'hurah-domain' === $packageType ;
    }
}
