<?php
namespace Madlines\Validation\Rule;

require_once __DIR__ . '/../../../../src/Madlines/Validation/RuleInterface.php';
require_once __DIR__ . '/../../../../src/Madlines/Validation/Rule/Len.php';

class LenTest extends \PHPUnit_Framework_TestCase
{
    public function validateDataProvider()
    {
        return [
            [
                'john',
                [
                    'min' => 3,
                    'max' => 7
                ],
                true
            ],
            [
                'jo',
                [
                    'min' => 3,
                    'max' => 7
                ],
                false
            ],
            [
                'johndoejunior',
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
        $rule = new \Madlines\Validation\Rule\Len();
        $result = $rule->validate(
            $input,
            'aFieldName',
            'Field\'s :field value\'s length must be number between :min and :max.',
            $params
        );

        if ($shouldPass) {
            $this->assertFalse($result);
        } else {
            $this->assertEquals(
                $result,
                'Field\'s aFieldName value\'s length must be number between ' . $params['min'] . ' and ' . $params['max'] . '.'
            );
        }
    }
}
