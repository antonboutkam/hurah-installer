<?php
namespace Hi\Installer\Domain;

use Composer\Composer;
use Composer\Installer\BinaryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\InstallerInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use Hi\Helpers\ConsoleColor;
use Hi\Helpers\StructureCreator;
use Hi\Installer\AbstractInstaller;
use Hi\Helpers\Console;
use Hi\Helpers\DirectoryStructure;
use Hi\Installer\Domain\Util;
use phpDocumentor\Reflection\Utils;

class Installer extends AbstractInstaller implements InstallerInterface
{

    /**
     * @var Console
     */
    private $console;

    function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null, BinaryInstaller $binaryInstaller = null)
    {
        parent::__construct($io, $composer, $type, $filesystem, $binaryInstaller);
        $this->console = new Console($io);

    }


    /**
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface $package
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        /**
         * Installing all files on the normal location inside vendor
         */
        parent::install($repo, $package);

        $sSystemId = $package->getExtra()['system_id'];
        $this->console->log("System id: $sSystemId", 'Novum domain installer');


        $sNamespace = Util::namespaceFromSystemId($sSystemId);
        $this->console->log("Generated namespace $sSystemId -> $sNamespace", 'Novum domain installer');

        Util::createBaseDirectoryStructure($this->io);


        // mkdit .domain/novum.svb
        $this->console->log('Creating public domain view');
        $this->makePublicDomainDir($sSystemId, $package);

        // symlinking all the files in the final system
        $this->createSymlinkMapping($sSystemId, $sNamespace);


        $this->linkInMigrateSh($sSystemId);
    }
    private function createSymlinkMapping(string $sSystemId, string $sNamespace)
    {
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
                $this->console->log('Source item missing, now creating <info>' . $oMapping->getSourcePath() . '</info>', 'Novum domain installer');
                $oMapping->createSource();
            }
            $sAbsoluteDestinationParentDir = dirname($oMapping->getDestPath());
            if(!is_dir($sAbsoluteDestinationParentDir))
            {
                $this->console->log("Creating destination parent directory <info>{$sAbsoluteDestinationParentDir}</info>",  'Novum domain installer');
                mkdir($sAbsoluteDestinationParentDir, 0777, true);
            }

            if(file_exists($oMapping->getDestPath() || is_link($oMapping->getDestPath())))
            {
                $this->console->log("Unlinking current destination <info>{$oMapping->getDestPath()}</info>",  'Novum domain installer');
                unlink($oMapping->getDestPath());
            }

            $this->console->log("Creating symlink  <info>{$oMapping->getSourcePath()}</info> --> <info>{$oMapping->getDestPath()}</info>",  'Novum domain installer');
            symlink($oMapping->getSourcePath(), $oMapping->getDestPath());
        }
    }
    private function linkInMigrateSh(string $sSystemId){
        $oDirectoryStructure = new DirectoryStructure();
        $sDestMigrationScript = "{$oDirectoryStructure->getSystemDir(false)}/build/database/{$sSystemId}/migrate.sh";
        $this->console->log("Adding migrate.sh script to $sDestMigrationScript",  'Novum domain installer');
        if(realpath($sDestMigrationScript))
        {
            unlink($sDestMigrationScript);
        }

        $this->console->log("Symlinking ---> ../../build/_skel/migrate.sh ----> $sDestMigrationScript");
        symlink( "{$oDirectoryStructure->getSystemDir(true)}/build/_skel/migrate.sh", $sDestMigrationScript);
    }

    private function makePublicDomainDir(string $sSystemId, PackageInterface $package)
    {
        $oDirectoryStructure = new DirectoryStructure();
        $sDomainsRoot = $oDirectoryStructure->getDomainDir(false);

        // ./domain
        if(!is_dir($sDomainsRoot))
        {
            $this->console->log("Creating public domain directory <info>$sDomainsRoot</info>");
            mkdir($sDomainsRoot, 0777, true);
        }
        else
        {
            $this->console->log("Public domain directory <info>$sDomainsRoot</info> exists");
        }
        $sDomainDir = $sDomainsRoot . '/' . $sSystemId;
        $sRelativeSource = $this->getRelativeInstallPath($package);

        if(is_link($sDomainDir))
        {
            $this->console->log("Domain was installed, unlinking, then re-linking <info>$sDomainDir</info>");
            unlink($sDomainDir);
        }
        // ./domain/novum.svb
        symlink($sRelativeSource, $sDomainDir);
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
