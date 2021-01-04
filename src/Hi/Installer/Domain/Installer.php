<?php

namespace Hi\Installer\Domain;

use Composer\Composer;
use Composer\Installer\BinaryInstaller;
use Composer\Installer\InstallerInterface;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use Hi\Helpers\Console;
use Hi\Helpers\DirectoryStructure;
use Hi\Installer\AbstractInstaller;

class Installer extends AbstractInstaller implements InstallerInterface
{

    /**
     * @var Console
     */
    private Console $console;

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

        // mkdir .domain/novum.svb
        $this->console->log('Creating public domain view');
        $this->makePublicDomainDir($sSystemId, $package);

        // symlinking all the files in the final system
        Util::createSymlinkMapping($this->console, $sSystemId, $sNamespace);

    }


    private function makePublicDomainDir(string $sSystemId, PackageInterface $package)
    {
        $oDirectoryStructure = new DirectoryStructure();
        $sDomainsRoot = $oDirectoryStructure->getDomainDir(false);

        // ./domain
        if (!is_dir($sDomainsRoot)) {
            $this->console->log("Creating public domain directory <info>$sDomainsRoot</info>");
            mkdir($sDomainsRoot, 0777, true);
        } else {
            $this->console->log("Public domain directory <info>$sDomainsRoot</info> exists");
        }
        $sDomainDir = $sDomainsRoot . '/' . $sSystemId;
        $sRelativeSource = $this->getRelativeInstallPath($package);

        if (is_link($sDomainDir)) {
            $this->console->log("Domain was installed, unlinking, then re-linking <info>$sDomainDir</info>");
            unlink($sDomainDir);
        }
        // ./domain/novum.svb
        $this->console->log("Creating symlink from: <info>$sRelativeSource</info> to: <info>$sDomainDir</info>");

        /**
         * The source path must be relative from the perspective of the destination. So ./domain/novum.svb should be
         * ../domain/novum.svb as it is seen from the domain path.
         */

        $sRelativeSourceSeenFromDestination = preg_replace('/^\.\//', '../', $sRelativeSource);
        symlink($sRelativeSourceSeenFromDestination, $sDomainDir);

    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {

    }


    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-domain' === $packageType || 'hurah-domain' === $packageType;
    }
}
