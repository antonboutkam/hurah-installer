<?php
namespace Hi\Installer\Domain;

use Composer\IO\IOInterface;
use Hi\Helpers\DirectoryStructure;
use Hi\Helpers\Console;
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

    static function createSymlinkMapping(Console $console, string $sSystemId, string $sNamespace)
    {
        /**
         * For every file there will be two mappings.
         *
         * 1. To the domain directory as seen from the root.
         * 2. Into the system directory to create the actual structure that the webserver loads.
         *
         * Important: all paths have to be relative, this is needed to make them work in both Docker and outside.
         */
        $oDirectoryStructure = new DirectoryStructure();
        $aSymlinkMapping = $oDirectoryStructure->getDomainSystemSymlinkMapping($sSystemId, $sNamespace);

        foreach ($aSymlinkMapping as $oSymlinkMapping)
        {

            if($oSymlinkMapping->sourceMissing() && $oSymlinkMapping->createIfNotExists())
            {
                $console->log('Source item missing, now creating <info>' . $oSymlinkMapping->getSourcePath() . '</info>', 'Novum domain installer');
                $oSymlinkMapping->createSource();
            }
            $sAbsoluteDestinationParentDir = dirname($oSymlinkMapping->getDestPath());
            if(!is_dir($sAbsoluteDestinationParentDir))
            {
                $console->log("Creating destination parent directory <info>{$sAbsoluteDestinationParentDir}</info>",  'Novum domain installer');
                mkdir($sAbsoluteDestinationParentDir, 0777, true);
            }

            if(file_exists($oSymlinkMapping->getDestPath() || is_link($oSymlinkMapping->getDestPath())))
            {
                $console->log("Unlinking current destination <info>{$oSymlinkMapping->getDestPath()}</info>",  'Novum domain installer');
                unlink($oSymlinkMapping->getDestPath());
            }

            $sDestinationPath =  Util::createRelativeSymlinkPath($oSymlinkMapping->getDestPath());
            $console->log("Creating symlink  <info>{$oSymlinkMapping->getSourcePath()}</info> --> <info>{$sDestinationPath}</info>",  'Novum domain installer');
            symlink($oSymlinkMapping->getSourcePath(), $sDestinationPath);
        }
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
