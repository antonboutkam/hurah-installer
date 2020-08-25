<?php

namespace Hi\Helpers;

use Composer\IO\IOInterface;

class StructureCreator
{

    static function create(DirectoryStructure $oDirectoryStructure, IOInterface $io):void
    {


        if(!is_dir($oDirectoryStructure->getPublicDir()))
        {
            $io->write("Creating public directory " . $oDirectoryStructure->getSystemDir());
            mkdir($oDirectoryStructure->getPublicDir());
        }

    }
}
