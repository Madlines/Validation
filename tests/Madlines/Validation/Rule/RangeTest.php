<?php
namespace Madlines\Validation\Rule;

require_once __DIR__ . '/../../../../src/Madlines/Validation/RuleInterface.php';
require_once __DIR__ . '/../../../../src/Madlines/Validation/Rule/Range.php';

class RangeTest extends \PHPUnit_Framework_TestCase
{
    public function validateDataProvider()
    {
        return [
            [
                4,
                [
                    'min' => 3,
                    'max' => 7
                ],
                true
            ],
            [
                2,
                [
                    'min' => 3,
                    'max' => 7
                ],
                false
            ],
            [
                8,
                [
                    'min' => 3,
                    'max' => 7
                ],
                false
            ],
        ];
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate($input, $params, $shouldPass)
    {
        $rule = new \Madlines\Validation\Rule\Range();
        $result = $rule->validate(
            $input,
            'aFieldName',
            'Field\'s :field value\'s must be number between :min and :max.',
            $params
        );

        if ($shouldPass) {
            $this->assertFalse($result);
        } else {
            $this->assertEquals(
                $result,
                'Field\'s aFieldName value\'s must be number between ' . $params['min'] . ' and ' . $params['max'] . '.'
            );
        }
    }
}
