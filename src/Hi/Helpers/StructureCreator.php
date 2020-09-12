<?php

namespace Hi\Helpers;

use Composer\IO\IOInterface;

class StructureCreator
{

    static function create(DirectoryStructure $oDirectoryStructure, IOInterface $io):void
    {
        $oConsole = new Console($io);

        if(!is_dir($oDirectoryStructure->getDomainDir()))
        {
            $oConsole->log('Creating domain directory ' . $oDirectoryStructure->getDomainDir(), 'Novum file structure');
            mkdir($oDirectoryStructure->getDomainDir(), 0777, true);
        }

        if(!is_dir($oDirectoryStructure->getSystemDir()))
        {
            $oConsole->log('Creating system directory ' . $oDirectoryStructure->getSystemDir(), 'Novum file structure');
            mkdir($oDirectoryStructure->getSystemDir(), 0777, true);
        }

        if(!is_dir($oDirectoryStructure->getPublicDir()))
        {
            $oConsole->log('Creating public directory ' . $oDirectoryStructure->getPublicDir(), 'Novum file structure');
            mkdir($oDirectoryStructure->getPublicDir(), 0777, true);
        }
    }
}
