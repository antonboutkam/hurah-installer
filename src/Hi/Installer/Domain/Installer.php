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
        $oConsole = new Console($this->io);

        $sSystemId = str_replace('/', '.', str_replace('domain-', '', $package->getName()));
        $oConsole->log("Generated system id: $sSystemId", 'Novum domain installer');
        /**
         * Installing all files on the normal location inside vendor
         */
        parent::install($repo, $package);
        $oConsole = new Console($this->io);
        /**
         * Generating a namespace
         */
        list($sOrg, $sDomain) = explode('.', str_replace('domain-', '', $sSystemId));
        $sDomainNsPart = preg_replace("/[^A-Za-z0-9 ]/", '_', $sDomain);
        $sNamespace = ucfirst($sOrg).ucfirst($sDomainNsPart);

        $oConsole->log("Generated namespace $sSystemId -> $sNamespace", 'Novum domain installer');

        /**
         * Create required directories
         */
        $oConsole->log("Setting up base file structure", 'Novum domain installer');
        $oDirectoryStructure = new DirectoryStructure();
        StructureCreator::create($oDirectoryStructure, $this->io);

        $aMapping = $oDirectoryStructure->getDomainSystemSymlinkMapping($sSystemId, $sNamespace);

        foreach ($aMapping as $sFrom => $sTo)
        {
            $sParentDir = dirname($sTo);
            if(!is_dir($sParentDir))
            {
                mkdir($sParentDir, 0777, true);
                $oConsole->log('Creating directory ' . $sParentDir, 'Novum domain installer');
            }

            $iDirsUp = substr_count($sTo, DIRECTORY_SEPARATOR) + 2; // + ./vendor/novum


            if(file_exists($sTo))
            {
                unlink($sTo);
            }

            $sRelativeInstallPath = $this->getRelativeInstallPath($package) . '/' . $sFrom;
            $oConsole->log('Symlinking ' . $iDirsUp .' ' . $sRelativeInstallPath . ' => ' . $sTo, 'Novum domain installer');

            symlink($sRelativeInstallPath, $sTo);

        }

        /**
         * Create public symlink
         */
        $sDomainsRoot = $oDirectoryStructure->getDomainDir();

        if(!is_dir($sDomainsRoot))
        {
            mkdir($sDomainsRoot, 0777, true);
        }

        $iDirsUp = 1;
        $sDomainDir = $sDomainsRoot . '/' . $sSystemId;
        $oConsole->log('Creating public view ' . $this->getRelativeInstallPath($package, $iDirsUp). ' => ' . $sDomainDir, 'Novum domain installer');
        symlink($this->getRelativeInstallPath($package, $iDirsUp), $sDomainDir);

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
