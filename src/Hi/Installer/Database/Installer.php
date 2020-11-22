<?php
namespace Hi\Installer\Database;

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
use Cli\Composer\Helper\Application\ApplicationLoader;
use Symfony\Component\Console\Command\Command;

class Installer extends AbstractInstaller implements InstallerInterface
{

    private $label = "Novum database installer";
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

        $oApplicationLoader = new ApplicationLoader();
        $oApplication = $oApplicationLoader->load();

        $command = $oApplication->find('db:make');
        $sSystemId = $package->getExtra()['system_id'];

        $searchInput = new ArrayInput(['domain' => [$sSystemId]]);
        $returnCode = $command->run($searchInput, $output);

        if($returnCode == Command::SUCCESS)
        {
            $this->console->log("Database initialized succesfully", $this->label);
        }
        else
        {
            $this->console->log("<error>Database initialisation failed succesfully</error>", $this->label);
        }

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
