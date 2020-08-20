<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 2-2-20
 * Time: 23:44
 */

namespace Test\Classes\Ui;

use Ui\Form\GenericFieldTypes;
use Ui\Form\FieldFactory;
use PHPUnit\Framework\TestCase;

class FieldFactoryTest extends TestCase
{

    public function testGetGenericFieldTypes()
    {
        $aFields = GenericFieldTypes::getAll();
        $this->assertTrue(is_array($aFields));
        $this->assertTrue(isset($aFields['postcode']));

        //$oFieldFactory =
    }

    public function testFromArray()
    {
        $aFields = [
            ['title' => 'Naam', 'name' => 'title', 'icon' => 'tag', 'type' => 'string', 'value' => 'blablab'],
            ['title' => 'Icoon', 'name' => 'icoon', 'icon' => 'image', 'type' => 'lookup'],
            ['type' => 'hidden', 'name' => 'component_key', 'value' => 'blablab'],
            ['type' => 'hidden', 'name' => 'component_type']
        ];

        FieldFactory::fromArray($aFields);
    }
}
