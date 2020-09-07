<?php

namespace Hi\Helpers;

class DirectoryStructure
{
    private $sSystemDir;
    private $sPublicDir;
    private $sDomainDir;

    public function __construct()
    {
        $sRootDir = dirname(dirname(dirname(__DIR__)));
        $sStructureFile =  "$sRootDir/directory-structure.json";
        $sStructureJson = file_get_contents($sStructureFile);
        $aStructure = json_decode($sStructureJson, true);

        $this->sSystemDir = $aStructure['system_dir'];
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
    function getDomainDir():string
    {
        return $this->sDomainDir;
    }

    function getSystemDir():string
    {
        return $this->sSystemDir;
    }

    public function getDomainSystemSymlinkMapping(string $sSystemId, string $sCustomNamespace):array
    {
        return [
            'admin_modules' => $this->sSystemDir . '/admin_modules/Custom/' . $sCustomNamespace,
            'classes/Crud' => $this->sSystemDir . '/classes/Crud/Custom/' . $sCustomNamespace,
            'classes/Model' => $this->sSystemDir . '/classes/Model/Custom/' . $sCustomNamespace,
            'style' => $this->sSystemDir . '/admin_public_html/Custom/' . $sSystemId,
            'schema.xml' => $this->sSystemDir . '/build/database/' . $sSystemId . '/schema.xml',
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
