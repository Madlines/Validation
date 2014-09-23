<?php
namespace Madlines\Validation;

require_once __DIR__ . '/../../../src/Madlines/Validation/Factory.php';
require_once __DIR__ . '/../../../src/Madlines/Validation/Field.php';
require_once __DIR__ . '/../../../src/Madlines/Validation/ValidationException.php';

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = new \Madlines\Validation\Factory();

        $created = $factory->create('Field', ['TheField', $factory]);
        $this->assertTrue($created instanceof \Madlines\Validation\Field);

        $created = $factory->create('ValidationException', ['The Error Message']);
        $this->assertTrue($created instanceof \Madlines\Validation\ValidationException);
    }
}
