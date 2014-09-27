<?php
namespace Madlines\Validation;

require_once __DIR__ . '/../../../src/Madlines/Validation/Factory.php';
require_once __DIR__ . '/../../../src/Madlines/Validation/Field.php';
require_once __DIR__ . '/../../../src/Madlines/Validation/FilterInterface.php';
require_once __DIR__ . '/../../../src/Madlines/Validation/RuleInterface.php';

require_once __DIR__ . '/FieldTest/AppendAStub.php';
require_once __DIR__ . '/FieldTest/AppendBStub.php';
require_once __DIR__ . '/FieldTest/IncorrectFilterStub.php';
require_once __DIR__ . '/FieldTest/IsFoobarStub.php';
require_once __DIR__ . '/FieldTest/IsNotLoremStub.php';
require_once __DIR__ . '/FieldTest/IncorrectRuleStub.php';

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $reflection = new \ReflectionClass($field);
        $name = $reflection->getProperty('name');
        $name->setAccessible(true);

        $this->assertEquals($name->getValue($field), 'TheField');
    }

    public function testAddRule()
    {
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $reflection = new \ReflectionClass($field);
        $rules = $reflection->getProperty('rules');
        $rules->setAccessible(true);

        $this->assertEmpty($rules->getValue($field));

        $fieldReference = $field->addRule('myrule', 'The Message', ['foo' => 'bar']);
        $this->assertEquals(
            $rules->getValue($field),
            [
                'myrule' => [
                    'message' => 'The Message',
                    'params' => [
                        'foo' => 'bar'
                    ]
                ]
            ]
        );
        $this->assertSame($field, $fieldReference);

        $field->addRule('anotherrule', 'Another Message', ['lorem' => 'ipsum']);
        $this->assertEquals(
            $rules->getValue($field),
            [
                'myrule' => [
                    'message' => 'The Message',
                    'params' => [
                        'foo' => 'bar'
                    ]
                ],
                'anotherrule' => [
                    'message' => 'Another Message',
                    'params' => [
                        'lorem' => 'ipsum'
                    ]
                ]
            ]
        );

        // Test that field class won't add any rules more that once
        $field->addRule('anotherrule', 'Something else', ['lorem' => 'ipsum']);
        $this->assertEquals(
            $rules->getValue($field),
            [
                'myrule' => [
                    'message' => 'The Message',
                    'params' => [
                        'foo' => 'bar'
                    ]
                ],
                'anotherrule' => [
                    'message' => 'Another Message',
                    'params' => [
                        'lorem' => 'ipsum'
                    ]
                ]
            ]
        );
    }

    public function testAddFilter()
    {
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $reflection = new \ReflectionClass($field);
        $filters = $reflection->getProperty('filters');
        $filters->setAccessible(true);

        $this->assertEmpty($filters->getValue($field));

        $fieldReference = $field->addFilter('myfilter');
        $this->assertEquals(
            $filters->getValue($field),
            [
                'myfilter'
            ]
        );
        $this->assertSame($field, $fieldReference);

        $field->addFilter('anotherfilter');
        $this->assertEquals(
            $filters->getValue($field),
            [
                'myfilter',
                'anotherfilter'
            ]
        );

        // test that function addFiltes doesn't add anything twice
        $field->addFilter('myfilter');
        $this->assertEquals(
            $filters->getValue($field),
            [
                'myfilter',
                'anotherfilter'
            ]
        );
    }

    /**
     * We're testing a mechanism of loading and executing filters,
     * not the filters themselves.
     */
    public function testFilter()
    {
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $field
            ->addFilter('AppendAStub')
            ->addFilter('AppendBStub');

        $filtered = $field->filter('value');
        $this->assertEquals('valueAB', $filtered);

        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $field
            ->addFilter('AppendBStub')
            ->addFilter('AppendAStub');

        $filtered = $field->filter('value');
        $this->assertEquals('valueBA', $filtered);

        $exceptionThrown = false;
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $field->addFilter('NonExistingFilter');

        try {
            $field->filter('value');
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);

        $exceptionThrown = false;
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $field->addFilter('IncorrectFilter');

        try {
            $field->filter('value');
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testValidate()
    {
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $field
            ->required('Field :field is required')
            ->addRule('isNotLoremStub', 'Field :field must not be lorem', [])
            ->addRule('isFoobarStub', 'Field :field has to be a foobar', []);

        $result = $field->validate('foobar');
        $this->assertFalse($result); // false meaning no errors

        $result = $field->validate('nofoobar');
        $this->assertEquals(['Field TheField has to be a foobar'], $result);

        $result = $field->validate(null);
        $this->assertEquals(['Field TheField is required'], $result);

        $result = $field->validate('lorem');
        $this->assertEquals(
            [
                'Field TheField must not be lorem',
                'Field TheField has to be a foobar'
            ],
            $result
        );

        $exceptionThrown = false;
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $field->addRule('NonExisting', 'Field :field has to be a foobar', []);

        try {
            $field->validate('anyvalue');
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);

        $exceptionThrown = false;
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $field->addRule('incorrectRuleStub', 'Field :field has to be a foobar', []);

        try {
            $field->validate('anyvalue');
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testRequired()
    {
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $reflection = new \ReflectionClass($field);
        $required = $reflection->getProperty('required');
        $required->setAccessible(true);
        $requiredMessage = $reflection->getProperty('requiredMessage');
        $requiredMessage->setAccessible(true);

        $this->assertFalse($required->getValue($field));

        $field->required('Field :field is required');
        $this->assertTrue($required->getValue($field));
        $this->assertEquals($requiredMessage->getValue($field), 'Field :field is required');
    }

    public function testForceError()
    {
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $reflection = new \ReflectionClass($field);
        $forceError = $reflection->getProperty('forceError');
        $forceError->setAccessible(true);
        $forceErrorMessage = $reflection->getProperty('forceErrorMessage');
        $forceErrorMessage->setAccessible(true);

        $this->assertFalse($forceError->getValue($field));

        $field->forceError('Field :field - forced error');
        $this->assertTrue($forceError->getValue($field));
        $this->assertEquals($forceErrorMessage->getValue($field), 'Field :field - forced error');
    }

    public function testDefaults()
    {
        $field = new \Madlines\Validation\Field('TheField', new \Madlines\Validation\Factory());
        $this->assertNull($field->getDefault());

        $field->setDefault('foo');
        $this->assertEquals('foo', $field->getDefault());
    }
}
