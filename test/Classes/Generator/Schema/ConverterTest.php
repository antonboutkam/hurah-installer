<?php
namespace Test\Classes\Api\Accounting\MoneyMonk;

use Core\Notification;
use Core\NotificationAction;
use Exception\LogicException;
use Generator\Schema\Converter;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testChangeXsd()
    {

        $sInput = '<database name="hurah" custom="NovumOverheid" crudRoot="Custom/NovumOverheid" crudNamespace="Crud\Custom\NovumOverheid" defaultIdMethod="native" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../schema/schema-plus-crud.xsd" >';
        $sExpectedOutput = '<database name="hurah" custom="NovumOverheid" crudRoot="Custom/NovumOverheid" crudNamespace="Crud\Custom\NovumOverheid" defaultIdMethod="native" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="./schema.xsd" >';

        $oConverter  = new Converter('');
        $oConverter->changeXsd($sInput);

        $this->assertTrue($sInput == $sExpectedOutput, $sInput . ' ==  '. $sExpectedOutput);

        $sInput = 'xsi:noNamespaceSchemaLocation="../../../schema/schema-plus-crud.xsd"';
        $sExpectedOutput = 'xsi:noNamespaceSchemaLocation="./schema.xsd"';

        $oConverter  = new Converter('');
        $oConverter->changeXsd($sInput);

        $this->assertTrue($sInput == $sExpectedOutput, $sInput . ' ==  '. $sExpectedOutput);
    }
    function testGetExternalSchemas()
    {
        $oConverter = new Converter('./test-schema.xml');

        $sSchema = file_get_contents('./test-schema.xml');
        $aSchemas = $oConverter->getExternalSchemaFiles($sSchema);
        $this->assertTrue(count($aSchemas) == 2, print_r($aSchemas, true));
    }
    function testFixExternalSchemas()
    {
        $aTestData = [
                0 => [
                    'in' => '<external-schema filename="../../schema/core-schema-extra.xml" />',
                    'out' => '<external-schema filename="./core-schema-extra.xml" />',

                ],
                1 => [
                    'in' => '<external-schema filename="./core-schema.xml" />',
                    'out' => '<external-schema filename="./core-schema.xml" />',

                ]
        ];
        foreach($aTestData as $sTestRow)
        {
            $oConverter  = new Converter('');
            $aSchemas = $oConverter->getExternalSchemaFiles($sTestRow['in']);
            $oConverter->adjustExternalSchemaPaths($sTestRow['in'], $aSchemas);

            $this->assertTrue($sTestRow['in'] == $sTestRow['out'], $sTestRow['in'] . '==' . $sTestRow['out']);
        }


    }
}
