<?php
namespace Madlines\Validation\Rule;

require_once __DIR__ . '/../../../../src/Madlines/Validation/RuleInterface.php';
require_once __DIR__ . '/../../../../src/Madlines/Validation/Rule/Regexp.php';

class RegexpTest extends \PHPUnit_Framework_TestCase
{
    public function validateDataProvider()
    {
        return [
            [
                'test',
                '/^[a-z]+$/',
                true
            ],
            [
                'Test',
                '/^[a-z]+$/',
                false
            ],
            [
                'Test',
                '/^[a-z]+$/i',
                true
            ],
        ];
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate($input, $regexp, $shouldPass)
    {
        $rule = new \Madlines\Validation\Rule\Regexp();
        $result = $rule->validate(
            $input,
            'aFieldName',
            'Field :field must match the pattern',
            ['regexp' => $regexp]
        );

        if ($shouldPass) {
            $this->assertFalse($result);
        } else {
            $this->assertEquals(
                $result,
                'Field aFieldName must match the pattern'
            );
        }
    }

    public function testIfValidationsThrowWrongParamsException()
    {
        $rule = new \Madlines\Validation\Rule\Regexp();
        $exceptionThrown = false;

        try {
            $result = $rule->validate(
                $input,
                'aFieldName',
                'Field :field must match the pattern',
                []
            );
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }
}
