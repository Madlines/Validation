<?php
namespace Madlines\Validation\Rule;

require_once __DIR__ . '/../../../../src/Madlines/Validation/RuleInterface.php';
require_once __DIR__ . '/../../../../src/Madlines/Validation/Rule/Email.php';

class EmailTest extends \PHPUnit_Framework_TestCase
{
    public function validateDataProvider()
    {
        // I'm not specifying more possibilities, because in the end
        // the rule uses PHP's filter_var and I assume that that's already tested.
        return [
            [
                'johndoe@example.com',
                true
            ],
            [
                'johndoe',
                false
            ],
        ];
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate($input, $shouldPass)
    {
        $rule = new \Madlines\Validation\Rule\Email();
        $result = $rule->validate(
            $input,
            'aFieldName',
            'Field :field must be a valid email address'
        );

        if ($shouldPass) {
            $this->assertFalse($result);
        } else {
            $this->assertEquals(
                $result,
                'Field aFieldName must be a valid email address'
            );
        }
    }
}
