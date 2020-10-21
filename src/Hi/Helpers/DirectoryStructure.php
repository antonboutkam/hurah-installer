<?php

namespace Hi\Helpers;

use Composer\Command\ProhibitsCommand;
use Core\Environment;
use Hi\Installer\Domain\Mapping;

class DirectoryStructure
{
    private $sSystemRoot;
    private $sSystemDir;
    private $sDataDir;
    private $sPublicDir;
    private $sDomainDir;
    private $sEnvDir;
    private $sLogDir;
    private $sSchemaXsdDir;

    public function __construct()
    {
        $sPackageDir = dirname(__DIR__, 3);
        $sStructureFile =  "$sPackageDir/directory-structure.json";
        $sStructureJson = file_get_contents($sStructureFile);
        $aStructure = json_decode($sStructureJson, true);

        $this->sSystemRoot = $_SERVER['SYSTEM_ROOT'];
        $this->sEnvDir = $aStructure['env_dir'];
        $this->sSystemDir = $aStructure['system_dir'];
        $this->sDataDir = $aStructure['data_dir'];
        $this->sPublicDir = $aStructure['public_dir'];
        $this->sDomainDir = $aStructure['domain_dir'];
        $this->sLogDir = $aStructure['log_dir'];
        $this->sSchemaXsdDir = $aStructure['schema_xsd_dir'];
    }
    function getSystemRoot():string
    {
        return $this->sSystemRoot;
    }
    function getPublicSitePath(string $sSiteDir, int $iDirsUp = null):string
    {
        if(!$iDirsUp)
        {
            $iDirsUp = 0;
        }
        return str_repeat('../', $iDirsUp) . $this->getPublicDir() . '/' . $sSiteDir;
    }
    function getSystemSitePath(string $sSiteDir):string
    {
        return $this->getSystemDir() . '/public_html/' . $sSiteDir;
    }
    function getSchemaXsdDir():string
    {
        return $this->sSchemaXsdDir;
    }
    function getPublicDir():string
    {
        return $this->sPublicDir;
    }
    function getDataDir():string
    {
        return $this->sDataDir;
    }
    function getLogDir():string
    {
        return $this->sLogDir;
    }
    function getDomainDir(bool $bAbsolute = false):string
    {
        if($bAbsolute)
        {
            return $this->getSystemRoot() . DIRECTORY_SEPARATOR . $this->sDomainDir;
        }
        return $this->sDomainDir;
    }

    function getSystemDir(bool $bAbsolute = false):string
    {
        if($bAbsolute)
        {
            return $this->getSystemRoot() . DIRECTORY_SEPARATOR . $this->sSystemDir;
        }
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
        if(!file_exists($this->getDomainDir()))
        {
            return [];
        }
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

    /**
     * @param string $sSystemId
     * @param string $sCustomNamespace
     * @return Mapping[]
     */
    public function getDomainSystemSymlinkMapping(string $sSystemId, string $sCustomNamespace):array
    {
        return [
            new Mapping($sSystemId, 'admin_modules', '/admin_modules/Custom/' . $sCustomNamespace, Mapping::DIRECTORY),
            new Mapping($sSystemId, 'classes/Crud', '/classes/Crud/Custom/' . $sCustomNamespace, Mapping::DIRECTORY),
            new Mapping($sSystemId, 'classes/Model', '/classes/Model/Custom/' . $sCustomNamespace, Mapping::DIRECTORY),
            new Mapping($sSystemId, 'style', '/admin_public_html/Custom/' . $sSystemId, Mapping::DIRECTORY),
            new Mapping($sSystemId, 'schema.xml', '/build/database/' . $sSystemId . '/schema.xml', Mapping::FILE),
            new Mapping($sSystemId, 'api.xml', '/build/database/' . $sSystemId . '/api.xml', Mapping::FILE),
            new Mapping($sSystemId, 'database/init', '/build/database/' . $sSystemId . '/crud_queries', Mapping::DIRECTORY),
            new Mapping($sSystemId, 'config.php', '/config/' . $sSystemId . '/config.php', Mapping::FILE)
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
