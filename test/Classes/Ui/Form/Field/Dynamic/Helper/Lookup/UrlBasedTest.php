<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 3-2-20
 * Time: 11:47
 */

namespace Test\Classes\Ui\Form\FieldHelper\Lookup;

use Core\DataType\Path;
use PHPUnit\Framework\TestCase;
use Ui\Form\Field\Dynamic\Helper\Lookup\UrlBased;

class PropelMethodTest extends TestCase
{
    private static $bIsSetUp = false;

    /*
        private function getMockData(): string
        {
           print_r(glob('./*'));
        }
    */
    public function setUp()
    {
        if (!self::$bIsSetUp) {
            self::$bIsSetUp = true;
            parent::setUp();
            $oPropelMethod = new UrlBased(new Path('./url-based-mock.json{naam}'));
            $aDefinition = [
                'function getLookups($mSelectedItem){', $oPropelMethod->getLookupsFunctionBody(), '}',
                'function getVisibleValue($iItemId){', $oPropelMethod->getVisibleValueFunctionBody(), '}'
            ];

            $sDefinition = join(PHP_EOL, $aDefinition);
            //echo $sDefinition;
            eval($sDefinition);
        }
    }


    public function testGetLookupsFunctionBody()
    {

        /** @noinspection PhpUndefinedFunctionInspection */
        $aResult = getLookups(1);

        $this->assertTrue(isset($aResult[0]['selected']) && $aResult[0]['selected'] === 'selected');
        $this->assertFalse(isset($aResult[2]['selected']));

        /** @noinspection PhpUndefinedFunctionInspection */
        $aResult = getLookups(3);;

        $this->assertTrue(isset($aResult[3]['label']));
        $this->assertTrue(isset($aResult[3]['id']));
        $this->assertTrue(isset($aResult[2]['label']));
        $this->assertTrue(isset($aResult[2]['id']));

        $this->assertTrue(isset($aResult[2]['selected']) && $aResult[2]['selected'] === 'selected');
        $this->assertFalse(isset($aResult[0]['selected']));
    }
}
