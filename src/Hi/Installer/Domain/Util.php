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

    /**
     * Adjusts relative symlink source paths need to be adjusted based on their destination.
     *
     * @param string $sSource
     * @param string $sDestination
     * @return string
     */
    static function createRelativeSymlinkPath(string $sSource, string $sDestination):string
    {
        $aLevels = explode(DIRECTORY_SEPARATOR, $sDestination);
        $sRelativePath = str_repeat('../', count($aLevels));
        $sRelativeSource = $sDirectory . $sSource;

        echo "Creating relative symlink $sRelativeSource $sRelativeDirectory " . PHP_EOL;
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
