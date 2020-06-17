<?php
namespace Hi\Installer\Domain\Component;

use Composer\IO\IOInterface;
use Core\QueryMapper;
use Hi\Exceptions\InstallationException;
use Hi\Installer\Domain\Component\Database\Db;
use \mysqli;

class Database
{
    /**
     * @param string $sDomainConfigFolder
     * @param string $sOriginalInstallPath
     * @param IOInterface $io
     * @throws InstallationException
     */
    public function install(array $aDatabaseProps, string $sOriginalInstallPath, IOInterface $io)
    {
        $io->write("Checking database availability.");
        Db::create($aDatabaseProps, $io);

    }
    public function uninstall(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io)
    {
       $io->write("Not removing database, you need to manually do this");
    }
}
