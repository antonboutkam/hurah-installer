<?php
namespace Test\Classes\Api\Accounting\MoneyMonk;

use Api\Accounting\MoneyMonk\Ledger;
use Exception\LogicException;
use PHPUnit\Framework\TestCase;


class LedgerTest extends TestCase
{
    function setUp()
    {
        $aConfigFiles = [];

        $aConfigFiles[] =  "../../../../../config/cockpit/propel/config.php";
        $aConfigFiles[] =  "../../../../../config/cockpit/config.php";
        foreach($aConfigFiles as $sConfigFile)
        {
            if(file_exists($sConfigFile))
            {
                require_once $sConfigFile;
            }
            else
            {
                throw new LogicException("File $sConfigFile does not exist");
            }
        }
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * @throws \Exception
     */
    public function testGetAllLedgers()
    {
        $aOutput = Ledger::getAll();
print_r($aOutput);
exit();
        $this->assertTrue(count($aOutput) > 10);

        $this->assertTrue(isset($aOutput[0]['kind']));
        $this->assertTrue(isset($aOutput[0]['code']));
        $this->assertTrue(isset($aOutput[0]['name']));

    }
}