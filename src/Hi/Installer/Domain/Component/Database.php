<?php
namespace Hi\Installer\Domain\Component;

use Composer\IO\IOInterface;
use Hi\Exceptions\InstallationException;

class Database
{
    /**
     * @param string $sDomainConfigFolder
     * @param string $sOriginalInstallPath
     * @param IOInterface $io
     * @throws InstallationException
     */
    public function install(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io)
    {

    }
    public function uninstall(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io)
    {
       $io->write("Not removing database, you need to manually do this");
    }
}
