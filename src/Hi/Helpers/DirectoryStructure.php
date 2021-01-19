<?php

namespace Hi\Helpers;

use DirectoryIterator;
use Hi\Installer\Domain\SymlinkMapping;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PluginType;

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
        $sStructureFile = "$sPackageDir/directory-structure.json";
        $sStructureJson = file_get_contents($sStructureFile);
        $aStructure = json_decode($sStructureJson, true);

        if (isset($_ENV['SYSTEM_ROOT'])) {
            $this->sSystemRoot = $_ENV['SYSTEM_ROOT'];
        } else if (isset($_SERVER['SYSTEM_ROOT'])) {
            $this->sSystemRoot = $_SERVER['SYSTEM_ROOT'];
        } else {
            $this->sSystemRoot = '/app';
        }

        $this->sEnvDir = $aStructure['env_dir'];
        $this->sSystemDir = $aStructure['system_dir'];
        $this->sDataDir = $aStructure['data_dir'];
        $this->sPublicDir = $aStructure['public_dir'];
        $this->sDomainDir = $aStructure['domain_dir'];
        $this->sLogDir = $aStructure['log_dir'];
        $this->sSchemaXsdDir = $aStructure['schema_xsd_dir'];
    }

    function getPluginRespositoryDir(PluginType $type, bool $bAbsolute = true): Path
    {
        $oRepositoryDir = Path::make($this->getDataDir($bAbsolute), 'repository', $type);
        $oRepositoryDir->makeDir();
        return $oRepositoryDir;
    }

    function getDataDir(bool $bAbsolute = false): string
    {
        if ($bAbsolute) {
            return $this->getSystemRoot() . DIRECTORY_SEPARATOR . $this->sDataDir;
        }
        return $this->sDataDir;
    }

    function getSystemRoot(): string
    {
        return $this->sSystemRoot;
    }

    /**
     * Returns the absolute path to the vendor directory
     * @return Path
     */
    function getVendorDir(): Path
    {
        return Path::make($this->getSystemRoot(), 'vendor');
    }

    function databaseDir(): Path
    {
        return Path::make($this->getSystemDir(), 'build', 'database');
    }

    function getSystemDir(bool $bAbsolute = false): string
    {
        if ($bAbsolute) {
            return $this->getSystemRoot() . DIRECTORY_SEPARATOR . $this->sSystemDir;
        }
        return $this->sSystemDir;
    }

    function getPublicSitePath(string $sSiteDir, int $iDirsUp = null): string
    {
        if (!$iDirsUp) {
            $iDirsUp = 0;
        }
        return str_repeat('../', $iDirsUp) . $this->getPublicDir() . '/' . $sSiteDir;
    }

    function getPublicDir(bool $bAbsolute = false): Path
    {
        if ($bAbsolute) {
            return Path::make($this->getSystemRoot(), $this->sPublicDir);
        }
        return new Path($this->sPublicDir);
    }

    function getSystemSitePath(string $sSiteDir): string
    {
        return $this->getSystemDir() . '/public_html/' . $sSiteDir;
    }

    function getSchemaXsdDir(): string
    {
        return $this->sSchemaXsdDir;
    }

    function getConfigRoot(bool $bAbsolute = false): Path
    {
        return Path::make($this->getSystemDir($bAbsolute), 'config');
    }

    function getLogDir(): string
    {
        return $this->sLogDir;
    }

    function getEnvDir(): string
    {
        return $this->sEnvDir;
    }

    /**
     * @return Domain[]
     */
    function getDomainCollection(): array
    {
        if (!file_exists($this->getDomainDir())) {
            return [];
        }
        $oDomainIterator = new DirectoryIterator($this->getDomainDir());
        $aOut = [];
        foreach ($oDomainIterator as $oDomainItem) {
            if (!$oDomainItem->isDir()) {
                continue;
            }
            if ($oDomainItem->isDot()) {
                continue;
            }
            $aOut[] = new Domain($oDomainItem);

        }
        return $aOut;
    }

    function getDomainDir(bool $bAbsolute = false): string
    {
        if ($bAbsolute) {
            return $this->getSystemRoot() . DIRECTORY_SEPARATOR . $this->sDomainDir;
        }
        return $this->sDomainDir;
    }

    /**
     * @param string $sSystemId
     * @param string $sCustomNamespace
     * @return SymlinkMapping[]
     */
    public function getDomainSystemSymlinkMapping(string $sSystemId, string $sCustomNamespace): array
    {
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
