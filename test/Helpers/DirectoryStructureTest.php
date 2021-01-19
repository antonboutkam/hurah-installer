<?php

namespace HiTest\Helpers;

use Hi\Helpers\DirectoryStructure;
use PHPUnit\Framework\TestCase;

class DirectoryStructureTest extends TestCase
{

    public function testGetSchemaXsdDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('build/schema', $oDirectoryStructure->getSchemaXsdDir());
    }

    public function testGetSystemRoot()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('/app', $oDirectoryStructure->getSystemRoot());
    }

    public function testGetPublicDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('public', "{$oDirectoryStructure->getPublicDir()}");
        $this->assertEquals('/app/public', "{$oDirectoryStructure->getPublicDir(true)}");
    }


    public function test__construct()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertInstanceOf(DirectoryStructure::class, $oDirectoryStructure);
    }

    public function testGetLogDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('data/log', "{$oDirectoryStructure->getLogDir()}");
    }

    public function testGetDataDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('data', "{$oDirectoryStructure->getDataDir()}");
    }

    public function testGetVendorDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('/app/vendor', "{$oDirectoryStructure->getVendorDir()}");
    }

    public function testGetPublicSitePath()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('public/xxx', "{$oDirectoryStructure->getPublicSitePath('xxx')}");
    }

    public function testGetSystemSitePath()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('.system/public_html/xxx', "{$oDirectoryStructure->getSystemSitePath('xxx')}");
    }

    public function testGetConfigRoot()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('/app/.system/config', "{$oDirectoryStructure->getConfigRoot(true)}");
    }

    public function testGetEnvDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('env', "{$oDirectoryStructure->getEnvDir()}");
    }

    public function testGetDomainDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('domain', "{$oDirectoryStructure->getDomainDir()}");
        $this->assertEquals('/app/domain', "{$oDirectoryStructure->getDomainDir(true)}");
    }

    public function testGetSystemCustomModulesPath()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('.system/admin_modules/Custom/xxx.yyy', "{$oDirectoryStructure->getSystemCustomModulesPath('xxx.yyy')}");
    }

    public function testGetSystemCustomCrudPath()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('.system/classes/Crud/Custom/xxx.yyy', "{$oDirectoryStructure->getSystemCustomCrudPath('xxx.yyy')}");
    }

    public function testDatabaseDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('.system/build/database', "{$oDirectoryStructure->databaseDir()}");
    }

    public function testGetSystemDir()
    {
        $oDirectoryStructure = new DirectoryStructure();
        $this->assertEquals('.system', "{$oDirectoryStructure->getSystemDir()}");
        $this->assertEquals('/app/.system', "{$oDirectoryStructure->getSystemDir(true)}");
    }
}
