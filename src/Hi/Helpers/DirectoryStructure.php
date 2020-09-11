<?php

namespace Hi\Helpers;

use Composer\Command\ProhibitsCommand;

class DirectoryStructure
{
    private $sSystemDir;
    private $sDataDir;
    private $sPublicDir;
    private $sDomainDir;
    private $sEnvDir;

    public function __construct()
    {
        $sRootDir = dirname(__DIR__, 3);
        $sStructureFile =  "$sRootDir/directory-structure.json";
        $sStructureJson = file_get_contents($sStructureFile);
        $aStructure = json_decode($sStructureJson, true);

        $this->sEnvDir = $aStructure['assets_dir'];
        $this->sSystemDir = $aStructure['system_dir'];
        $this->sDataDir = $aStructure['data_dir'];
        $this->sPublicDir = $aStructure['public_dir'];
        $this->sDomainDir = $aStructure['domain_dir'];

    }
    function getPublicSitePath(string $sSiteDir, int $iDirsUp = 0):string
    {
        return str_repeat('../', $iDirsUp) . $this->getPublicDir() . '/' . $sSiteDir;
    }
    function getSystemSitePath(string $sSiteDir):string
    {
        return $this->getSystemDir() . '/public_html/' . $sSiteDir;
    }
    function getPublicDir():string
    {
        return $this->sPublicDir;
    }
    function getDataDir():string
    {
        return $this->sDataDir;
    }
    function getDomainDir():string
    {
        return $this->sDomainDir;
    }

    function getSystemDir():string
    {
        return $this->sSystemDir;
    }
    function getEnvDir():string
    {
        return $this->sEnvDir;
    }

    /**
     * @return Domain[]
     */
    function getDomainCollection():array
    {
        $oDomainIterator = new \DirectoryIterator($this->getDomainDir());
        $aOut = [];
        foreach ($oDomainIterator as $oDomainItem)
        {
            if(!$oDomainItem->isDir())
            {
                continue;
            }
            if($oDomainItem->isDot())
            {
                continue;
            }
            $aOut[] = new Domain($oDomainItem);

        }
        return $aOut;
    }
    public function getDomainSystemSymlinkMapping(string $sSystemId, string $sCustomNamespace):array
    {
        return [
            'admin_modules' => $this->sSystemDir . '/admin_modules/Custom/' . $sCustomNamespace,
            'classes/Crud' => $this->sSystemDir . '/classes/Crud/Custom/' . $sCustomNamespace,
            'classes/Model' => $this->sSystemDir . '/classes/Model/Custom/' . $sCustomNamespace,
            'style' => $this->sSystemDir . '/admin_public_html/Custom/' . $sSystemId,
            'schema.xml' => $this->sSystemDir . '/build/database/' . $sSystemId . '/schema.xml',
            'api.xml' => $this->sSystemDir . '/build/database/' . $sSystemId . '/api.xml',
            'database/init' => $this->sSystemDir . '/build/database/' . $sSystemId . '/crud_queries',
            'config.php' => $this->sSystemDir . '/config/' . $sSystemId . '/config.php',
        ];
    }

    public function getSystemCustomModulesPath($sCustomNamespace)
    {
        return $this->sSystemDir . '/admin_modules/Custom/' . $sCustomNamespace;
    }
    public function getSystemCustomCrudPath($sCustomNamespace)
    {
        return $this->sSystemDir . '/classes/Crud/Custom/' . $sCustomNamespace;
    }

}
