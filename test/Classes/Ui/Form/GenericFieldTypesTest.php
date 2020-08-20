<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 2-2-20
 * Time: 23:48
 */

namespace Test\Classes\Ui\Form;

use Ui\Form\GenericFieldTypes;
use PHPUnit\Framework\TestCase;

class GenericFieldTypesTest extends TestCase
{

    public function testGetAll()
    {
        $aAll = GenericFieldTypes::getAll();

        $this->assertTrue(is_array($aAll));
        $this->assertTrue(isset($aAll['postcode']));
    }
}
