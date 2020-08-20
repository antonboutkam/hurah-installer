<?php

namespace HiTest\Installer\Domain\Component\Database;

use Hi\Installer\Domain\Component\Database\Db;
use phpunit\framework\TestCase;

class DbTest extends TestCase
{
    private function getMockDbLogin():array
    {
        return ['DB_USER' => 'x', 'DB_PASS' => 'y', 'DB_SERVER' => 'z', 'DB_NAME' => 'a'];
    }

    public function testDatabaseAndUserExist()
    {
        $oMockBuilder = $this->getMockBuilder(Db::class);
        $oMockBuilder->setMethods(['canConnectWithNormalParams', 'databaseExists']);
        $oMockDb = $oMockBuilder->getMock();

        $io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();


        $oMockDb->expects($this->once())->method('canConnectWithNormalParams')->with($this->getMockDbLogin())->willReturn(true);

        $oMockDb->expects($this->once())->method('databaseExists')->with($this->getMockDbLogin())->willReturn(true);

        $oMockDb->create($this->getMockDbLogin(), $io);

    }
    public function testUserDoesNotExist()
    {
        $oMockBuilder = $this->getMockBuilder(Db::class);
        $aMockMethods = ['canConnectWithNormalParams', 'envFileContainsRootLogin', 'createDatabaseWithRootLoginIfNotExists', 'createUserIfNotExists'];
        $oMockBuilder->setMethods($aMockMethods);
        $oMockDb = $oMockBuilder->getMock();

        $io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();


        $oMockDb->expects($this->exactly(2))->method('canConnectWithNormalParams')->with($this->getMockDbLogin())->willReturn(false);

        $oMockDb->expects($this->atLeastOnce())->method('envFileContainsRootLogin')->with($this->getMockDbLogin())->willReturn(true);

        $oMockDb->expects($this->atLeastOnce())->method('createDatabaseWithRootLoginIfNotExists')->with($this->getMockDbLogin())->willReturn(true);
        $oMockDb->expects($this->atLeastOnce())->method('createUserIfNotExists')->with($this->getMockDbLogin())->willReturn(true);


        $this->assertTrue($oMockDb->create($this->getMockDbLogin(), $io));
    }


}
