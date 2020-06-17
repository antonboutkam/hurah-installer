<?php
namespace Hi\Installer\Domain\Component\FileStructure;

use Composer\IO\IOInterface;

final class ConfigDir
{
    static function create(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io):void
    {
        $sConfigRoot = './system/config';
        if(!is_dir($sConfigRoot))
        {
            $io->write("Create dir $sConfigRoot");
            mkdir($sConfigRoot, 0777, true);
        }

        $sConfigDir = $sConfigRoot . '/' . $sDomainConfigFolder;
        if(!is_dir($sConfigDir))
        {
            $io->write("Create dir $sConfigDir");
            mkdir($sConfigDir, 0777, true);
        }

        $sConfigFileLocation = $sOriginalInstallPath . '/config.php';
        if(!file_exists($sConfigFileLocation))
        {
            $io->writeError("File missing config.php, this could be a bug");
            return;
        }

        $sConfigDest = $sConfigDir . '/config.php';
        if(!file_exists($sConfigDest))
        {
            $io->write('Creating symlink ' . $sConfigFileLocation, $sConfigDir . '/config.php');
            symlink($sConfigFileLocation, $sConfigDest);
        }
        else
        {
            $io->write('Config file was already symlinked, skipping');

        }

    }
    static function remove(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io):void
    {
        $sConfigRoot = './system/config/' . $sDomainConfigFolder . '/config.php';
        if(file_exists($sConfigRoot))
        {
            unlink($sConfigRoot);
        }
    }
}
