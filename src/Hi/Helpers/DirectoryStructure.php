<?php

namespace Hi\Helpers;

use Composer\Command\ProhibitsCommand;
use Core\DataType\PluginType;
use Core\Environment;
use Core\Utils;
use Hi\Installer\Domain\Mapping;
use Hi\Installer\Domain\SymlinkMapping;

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

    public function __construct(){
        $sPackageDir = dirname(__DIR__, 3);
        $sStructureFile =  "$sPackageDir/directory-structure.json";
        $sStructureJson = file_get_contents($sStructureFile);
        $aStructure = json_decode($sStructureJson, true);

        if(isset($_ENV['SYSTEM_ROOT']))
        {
            $this->sSystemRoot = $_ENV['SYSTEM_ROOT'];
        }
        else if(isset($_SERVER['SYSTEM_ROOT']))
        {
            $this->sSystemRoot = $_SERVER['SYSTEM_ROOT'];
        }
        else
        {
            $this->sSystemRoot = null;
        }

        $this->sEnvDir = $aStructure['env_dir'];
        $this->sSystemDir = $aStructure['system_dir'];
        $this->sDataDir = $aStructure['data_dir'];
        $this->sPublicDir = $aStructure['public_dir'];
        $this->sDomainDir = $aStructure['domain_dir'];
        $this->sLogDir = $aStructure['log_dir'];
        $this->sSchemaXsdDir = $aStructure['schema_xsd_dir'];
    }

    function getPluginRespositoryDir(PluginType $type, bool $bAbsolute = true)
    {
        $sRepositoryDir = Utils::makePath($this->getDataDir($bAbsolute), 'repository' ,$type);
        Utils::makeDir($sRepositoryDir);
        return $sRepositoryDir;
    }
    function getSystemRoot():string
    {
        return $this->sSystemRoot;
    }
    function getVendorDir():string
    {
        return Utils::makePath($this->getSystemRoot(), 'vendor');
    }
    function databaseDir():string
    {
        return Utils::makePath($this->getSystemDir(), 'build', 'database');
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
    function getDataDir(bool $bAbsolute = false):string
    {
        if($bAbsolute)
        {
            return $this->getSystemRoot() . DIRECTORY_SEPARATOR . $this->sDataDir;
        }
        return $this->sDataDir;
    }
    function getConfigRoot(bool $bAbsolute = false):string
    {
        return Utils::makePath($this->getSystemDir($bAbsolute), 'config');
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
     * @return SymlinkMapping[]
     */
    public function getDomainSystemSymlinkMapping(string $sSystemId, string $sCustomNamespace):array{
        return [
            new SymlinkMapping($sSystemId, 'admin_modules', 'admin_modules/Custom/' . $sCustomNamespace, SymlinkMapping::DIRECTORY),
            new SymlinkMapping($sSystemId, 'classes/Crud', 'classes/Crud/Custom/' . $sCustomNamespace, SymlinkMapping::DIRECTORY),
            new SymlinkMapping($sSystemId, 'classes/Model', 'classes/Model/Custom/' . $sCustomNamespace, SymlinkMapping::DIRECTORY),
            new SymlinkMapping($sSystemId, 'style', 'admin_public_html/custom/' . $sSystemId, SymlinkMapping::DIRECTORY),
            new SymlinkMapping($sSystemId, 'schema.xml', 'build/database/' . $sSystemId . '/schema.xml', SymlinkMapping::FILE),
            new SymlinkMapping($sSystemId, 'api.xml', 'build/database/' . $sSystemId . '/api.xml', SymlinkMapping::FILE),
            new SymlinkMapping($sSystemId, 'database/init', 'build/database/' . $sSystemId . '/crud_queries', SymlinkMapping::DIRECTORY),
            new SymlinkMapping($sSystemId, 'config.php', 'config/' . $sSystemId . '/config.php', SymlinkMapping::FILE)
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
