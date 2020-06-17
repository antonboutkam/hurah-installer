<?php
namespace Hi\Installer\Domain\Component\FileStructure;

use Composer\IO\IOInterface;
use Hi\Installer\Util;

final class BuildDir
{
    static function create(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io):void
    {
        $sBuildDir = './system/build';
        if(!is_dir($sBuildDir))
        {
            $io->write("Create dir $sBuildDir");
            mkdir($sBuildDir, 0777, true);
        }

        $sDomainPath = $sBuildDir . '/' . $sDomainConfigFolder;
        if(!is_dir($sDomainPath))
        {
            $io->write("Create dir $sDomainPath");
            mkdir($sDomainPath, 0777, true);
        }

        $sSchemaFileLocation = $sOriginalInstallPath . '/schema.xml';
        if(!file_exists($sSchemaFileLocation))
        {
            $io->writeError("File missing schema.xml, this could be a bug");
            return;
        }

        $io->write('Creating symlink ' . $sSchemaFileLocation, $sDomainPath . '/schema.xml');
        symlink($sSchemaFileLocation, $sDomainPath . '/schema.xml');

        if(!is_dir($sDomainPath . '/database'))
        {
            mkdir($sDomainPath . '/database');
            mkdir($sDomainPath . '/database/init');

        }
    }
    static function remove(string $sDomainConfigFolder, string $sOriginalInstallPath, IOInterface $io):void
    {
        $sBuildDir = './system/build/' . $sDomainConfigFolder;
        Util::removeSymlink($sBuildDir);
    }
}
