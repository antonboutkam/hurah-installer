<?php

namespace Hi\Installer\Domain\Component;

use Composer\IO\IOInterface;
use Hi\Exceptions\InstallationException;
use Hi\Installer\Domain\Component\FileStructure\BuildDir;
use Hi\Installer\Domain\Component\FileStructure\ConfigDir;
use Hi\Installer\Domain\Component\FileStructure\SysDir;

class FileStructure
{
    /**
     * @param string $sDomainConfigFolder
     * @param string $sOriginalInstallPath
     * @param IOInterface $io
     * @throws InstallationException
     */
    public function install(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io)
    {
        SysDir::create($sDomainConfigFolder, $sOriginalInstallPath, $io);
        BuildDir::create($sDomainConfigFolder, $sOriginalInstallPath, $io);
        ConfigDir::create($sDomainConfigFolder, $sOriginalInstallPath, $io);
    }
    public function uninstall(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io)
    {
        BuildDir::remove($sDomainConfigFolder, $sOriginalInstallPath, $io);
        ConfigDir::remove($sDomainConfigFolder, $sOriginalInstallPath, $io);

    }


}
