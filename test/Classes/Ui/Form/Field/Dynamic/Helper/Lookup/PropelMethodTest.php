<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 3-2-20
 * Time: 11:47
 */

namespace Test\Classes\Ui\Form\FieldHelper\Lookup;

use Model\Account\User;
use PHPUnit\Framework\TestCase;
use Propel\Runtime\Map\TableMap;
use Ui\Form\Field\Dynamic\Helper\Lookup\PropelMethod;

class PropelMethodTest extends TestCase
{

    private static $bIsSetUp = false;
    private function getMockData(): string
    {
        $aMockUsers = [
            ['id' => 1, 'first_name' => 'Hjalmar', 'last_name' => 'van der Schoot'], ['id' => 2, 'first_name' => 'Aernout', 'last_name' => 'Veldhuijzen'], ['id' => 3, 'first_name' => 'Ilse', 'last_name' => 'Veenema'], ['id' => 4, 'first_name' => 'Amé', 'last_name' => 'Stokkink'],];

        $aUserCollection = [];
        foreach ($aMockUsers as $aUser) {
            $oUser = new User();
            $oUser->fromArray($aUser, TableMap::TYPE_FIELDNAME);
            $aUserCollection[] = $oUser;
        }
        return serialize($aUserCollection);
    }
    public function setUp()
    {
        parent::setUp();
        if (!self::$bIsSetUp) {
            self::$bIsSetUp = true;
            $aDefinition = [
                'class MockQuery{', '   static function create(){', '       return new MockQuery();', '   }', '   function orderByFirstName(){', '       return $this;', '   }', '   function find(){', '       return unserialize(\'' . $this->getMockData() . '\');', '   }', '}'];
            $sDefinition = join(PHP_EOL, $aDefinition);

            eval($sDefinition);

            $oPropelMethod = new PropelMethod('\Mock', 'getFirstName');
            $aDefinition = [
                'function getLookups($mSelectedItem){',
                    $oPropelMethod->getLookupsFunctionBody(),
                '}'];

            $sDefinition = join(PHP_EOL, $aDefinition);
            //echo $sDefinition;
            eval($sDefinition);

            $aDefinition = [
                'function getVisibleValue($iItemId){',
                $oPropelMethod->getVisibleValueFunctionBody(),
                '}'
            ];
            $sDefinition = join(PHP_EOL, $aDefinition);
            echo $sDefinition;
            eval($sDefinition);
        }
    }

    /*
    public function getVisibleValueFunctionBody()
    {

        $aResult = getVisibleValue(1);
        print_r($aResult);
    }
    */
    public function testGetLookupsFunctionBody()
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        $aResult = getLookups(1);

        $this->assertTrue(isset($aResult[0]['selected']) && $aResult[0]['selected'] === 'selected');
        $this->assertFalse(isset($aResult[2]['selected']));

        /** @noinspection PhpUndefinedFunctionInspection */
        $aResult = getLookups(3);

        $this->assertTrue(isset($aResult[3]['label']));
        $this->assertTrue(isset($aResult[3]['id']));

        $this->assertTrue(isset($aResult[2]['selected']) && $aResult[2]['selected'] === 'selected');
        $this->assertFalse(isset($aResult[0]['selected']));

    }
}
