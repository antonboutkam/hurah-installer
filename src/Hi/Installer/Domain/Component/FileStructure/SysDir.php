<?php
namespace Hi\Installer\Domain\Component\FileStructure;

use Composer\IO\IOInterface;
use Hi\Exceptions\InstallationException;

final class SysDir
{
    static function create(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io):void
    {
        if(!is_dir('./system'))
        {
            throw new InstallationException("System dir missing, could not install files");
        }
    }

    static function remove(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io):void
    {

    }


}
