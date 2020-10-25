<?php
namespace Hi\Installer\Domain;

use Composer\IO\IOInterface;
use Hi\Helpers\StructureCreator;

class Util
{
    static function createBaseDirectoryStructure(IOInterface $io):void
    {
        /**
         * Create required root / base directories
         */
        $oDirectoryStructure = new DirectoryStructure();

        StructureCreator::create($oDirectoryStructure, $io);
    }

    static function relativeSymlinkPath(string $sDirectory)
    {
        $aLevels = explode(DIRECTORY_SEPARATOR, $sDirectory);
        $sRelativePath = str_repeat('../', count($aLevels));
        $sRelativeDirectory = $sDirectory . $sRelativePath;
        echo "Relative directory $sRelativeDirectory " . PHP_EOL;
        return $sRelativeDirectory;
    }

    static function namespaceFromSystemId(string $sSystemId):string
    {

        /**
         * Generating a namespace
         */
        list($sOrg, $sDomain) = explode('.', $sSystemId);
        $sDomainNsPart = preg_replace("/[^A-Za-z0-9 ]/", '_', $sDomain);
        $sNamespace = ucfirst($sOrg).ucfirst($sDomainNsPart);
        return $sNamespace;
    }

}
