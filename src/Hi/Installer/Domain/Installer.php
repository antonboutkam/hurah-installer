<?php
namespace Hi\Installer\Domain;

use Composer\Package\PackageInterface;
use Composer\Installer\InstallerInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Hi\Helpers\ConsoleColor;
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
        /**
         * Installing all files on the normal location inside vendor
         */
        $oConsole = new Console($this->io);
        parent::install($repo, $package);

        $sSystemId = $package->getExtra()['system_id'];
        $oConsole->log("System id: $sSystemId", 'Novum domain installer');

        /**
         * Generating a namespace
         */
        list($sOrg, $sDomain) = explode('.', str_replace('domain-', '', $sSystemId));
        $sDomainNsPart = preg_replace("/[^A-Za-z0-9 ]/", '_', $sDomain);
        $sNamespace = ucfirst($sOrg).ucfirst($sDomainNsPart);

        $oConsole->log("Generated namespace $sSystemId -> $sNamespace", 'Novum domain installer');

        /**
         * Create required root / base directories
         */
        $oConsole->log("Setting up base file structure", 'Novum domain installer');
        $oDirectoryStructure = new DirectoryStructure();
        StructureCreator::create($oDirectoryStructure, $this->io);

        $oConsole->log('Creating public domain view');
        // mkdit .domain/novum.svb
        $this->makePublicDomainDir($oConsole, $sSystemId, $package);

        /**
         * For every file there will be two mappings.
         *
         * 1. To the domain directory as seen from the root.
         * 2. Into the system directory to create the actual structure that the webserver loads.
         *
         * Important: all paths have to be relative, this is needed to make them work in both Docker and outside.
         */
        $aSymlinkMapping = $oDirectoryStructure->getDomainSystemSymlinkMapping($sSystemId, $sNamespace);

        foreach ($aMapping as $oMapping)
        {

            if($oMapping->sourceMissing() && $oMapping->createIfNotExists())
            {
                $oConsole->log('Source item missing, now creating <info>' . $oMapping->getSourcePath() . '</info>', 'Novum domain installer');
                $oMapping->createSource();
            }
            $sAbsoluteDestinationParentDir = dirname($oMapping->getDestPath());
            if(!is_dir($sAbsoluteDestinationParentDir))
            {
                $oConsole->log("Creating destination parent directory <info>{$sAbsoluteDestinationParentDir}</info>",  'Novum domain installer');
                mkdir($sAbsoluteDestinationParentDir, 0777, true);
            }

            if(file_exists($oMapping->getDestPath() || is_link($oMapping->getDestPath())))
            {
                $oConsole->log("Unlinking current destination <info>{$oMapping->getDestPath()}</info>",  'Novum domain installer');
                unlink($oMapping->getDestPath());
            }

            $oConsole->log("Creating symlink  <info>{$oMapping->getSourcePath()}</info> --> <info>{$oMapping->getDestPath()}</info>",  'Novum domain installer');
            symlink($oMapping->getSourcePath(), $oMapping->getDestPath());
        }

        $this->linkInMigrateSh($sSystemId);
    }
    private function linkInMigrateSh(string $sSystemId){
        $oDirectoryStructure = new DirectoryStructure();
        $sDestMigrationScript = "{$oDirectoryStructure->getSystemDir(false)}/build/database/{$sSystemId}/migrate.sh";
        $oConsole->log("Adding migrate.sh script to $sDestMigrationScript",  'Novum domain installer');
        if(realpath($sDestMigrationScript))
        {
            unlink($sDestMigrationScript);
        }

        $oConsole->log("Symlinking ---> ../../build/_skel/migrate.sh ----> $sDestMigrationScript");
        symlink( "{$oDirectoryStructure->getSystemDir(true)}/build/_skel/migrate.sh", $sDestMigrationScript);
    }

    private function makePublicDomainDir(Console $oConsole, string $sSystemId, PackageInterface $package)
    {
        $oDirectoryStructure = new DirectoryStructure();
        $sDomainsRoot = $oDirectoryStructure->getDomainDir(false);

        // ./domain
        if(!is_dir($sDomainsRoot))
        {
            $oConsole->log("Creating public domain directory <info>$sDomainsRoot</info>");
            mkdir($sDomainsRoot, 0777, true);
        }
        else
        {
            $oConsole->log("Public domain directory <info>$sDomainsRoot</info> exists");
        }
        $sDomainDir = $sDomainsRoot . '/' . $sSystemId;
        $sRelativeSource = $this->getRelativeInstallPath($package);

        if(is_link($sRelativeSource))
        {
            $oConsole->log("Domain was installed, unlinking, then re-linking <info>$sDomainsRoot</info>");
            unlink($sRelativeSource);
        }
        // ./domain/novum.svb
        symlink($this->getRelativeInstallPath($package), $sDomainDir);
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
