<?php
namespace Madlines\Validation;

require_once __DIR__ . '/../../../src/Madlines/Validation/Validation.php';
require_once __DIR__ . '/../../../src/Madlines/Validation/ValidationException.php';
require_once __DIR__ . '/../../../src/Madlines/Validation/Factory.php';
require_once __DIR__ . '/../../../src/Madlines/Validation/Field.php';

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    public function testField()
    {
        $validation = new \Madlines\Validation\Validation();
        $reflection = new \ReflectionClass($validation);
        $fields = $reflection->getProperty('fields');
        $fields->setAccessible(true);
        $factory = $reflection->getProperty('factory');
        $factory->setAccessible(true);

        $this->assertEmpty($fields->getValue($validation));
        $this->assertTrue($factory->getValue($validation) instanceof \Madlines\Validation\Factory);

        $field1Ref1 = $validation->field('field1');
        $field1Ref2 = $validation->field('field1');
        $field2 = $validation->field('field2');

        $this->assertSame($field1Ref1, $field1Ref2);
        $this->assertEquals($field1Ref1->getName(), 'field1');
        $this->assertEquals($field2->getName(), 'field2');
    }

    public function executeDataProvider()
    {
        return [
            [
                [
                    'The Error'
                ],
                false,
                false,
                [
                    'field1' => [
                        'The Error'
                    ]
                ]
            ],
            [
                false,
                [
                    'The Error'
                ],
                false,
                [
                    'field2' => [
                        'The Error'
                    ]
                ]
            ],
            [
                false,
                false,
                true,
                [
                    'field1' => 'foo',
                    'field2' => 'bar'
                ]
            ]
        ];
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute($field1Failed, $field2Failed, $validationPassed, $expectedOutput)
    {
        $validation = new \Madlines\Validation\Validation();
        $reflection = new \ReflectionClass($validation);
        $fields = $reflection->getProperty('fields');
        $fields->setAccessible(true);

        $field1 = $this->getMockBuilder('\Madlines\Validation\Field')
            ->setConstructorArgs(['field1', new \Madlines\Validation\Factory()])
            ->getMock();

        $field1
            ->expects($this->any())
            ->method('filter')
            ->will($this->returnArgument(0));

        $field1
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue($field1Failed));

        $field2 = $this->getMockBuilder('\Madlines\Validation\Field')
            ->setConstructorArgs(['field2', new \Madlines\Validation\Factory()])
            ->getMock();

        $field2->expects($this->any())
            ->method('filter')
            ->will($this->returnArgument(0));

        $field2
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue($field2Failed));

        $fields->setValue(
            $validation,
            [
                'field1' => $field1,
                'field2' => $field2,
            ]
        );

        try {
            $output = $validation->execute(
                [
                    'field1' => 'foo',
                    'field2' => 'bar',
                    'field3' => 'shallbeignored',
                ]
            );
            $this->assertEquals($output, $expectedOutput);
            $this->assertTrue($validationPassed);
        } catch (\Madlines\Validation\ValidationException $e) {
            $this->assertFalse($validationPassed);
            $this->assertEquals($e->getErrorMessages(), $expectedOutput);
        }
    }

    public function testValidationAddsAllMissingFieldsAsEmpty()
    {
        $validation = new \Madlines\Validation\Validation();
        $reflection = new \ReflectionClass($validation);
        $fields = $reflection->getProperty('fields');
        $fields->setAccessible(true);

        $field1 = $this->getMockBuilder('\Madlines\Validation\Field')
            ->setConstructorArgs(['field1', new \Madlines\Validation\Factory()])
            ->getMock();

        $field1
            ->expects($this->any())
            ->method('filter')
            ->will($this->returnArgument(0));

        $field1
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(false));

        $fields->setValue(
            $validation,
            [
                'field1' => $field1,
            ]
        );

        $passed = false;
        try {
            $output = $validation->execute([]);
            $this->assertEquals(
                $output,
                [
                    'field1' => null
                ]
            );
            $passed = true;
        } catch (\Madlines\Validation\ValidationException $e) {
            $passed = false;
        }

        $this->assertTrue($passed);
    }

    public function testConstructor()
    {
        $params = [
            'field1' => [
                'filters' => [
                    'foo', 'bar', 'lorem'
                ],
                'rules' => [
                    'dolor' => [
                        'Lipsum :field at dolor',
                        [
                            'fii' => 'bir',
                            'lorum' => 'forum',
                        ]
                    ],
                    'color' => [
                        'Lipsum :field at color',
                        [
                            'fixi' => 'biri',
                            'loxum' => 'foxum',
                        ]
                    ],
                ],
            ],
            'field2' => [
                'required' => 'Field :field is required',
                'filters' => [
                    'foo', 'bar'
                ],
                'rules' => [
                    'tolor' => [
                        'Lipsum :field at tolor',
                        [
                            'tli' => 'ibir',
                            'lokum' => 'fotum',
                        ]
                    ],
                ],
            ],
        ];

        $validation = new \Madlines\Validation\Validation($params);
        $reflection = new \ReflectionClass($validation);
        $fields = $reflection->getProperty('fields');
        $fields->setAccessible(true);
        $fieldsObjects = $fields->getValue($validation);

        $this->assertTrue(isset($fieldsObjects['field1']));
        $this->assertTrue(isset($fieldsObjects['field2']));

        $field1Alt = new \Madlines\Validation\Field('field1', new \Madlines\Validation\Factory());
        foreach ($params['field1']['filters'] as $filter) {
            $field1Alt->addFilter($filter);
        }

        foreach ($params['field1']['rules'] as $ruleName => $rule) {
            $field1Alt->addRule($ruleName, $rule[0], $rule[1]);
        }

        $this->assertEquals($fieldsObjects['field1'], $field1Alt);

        $field2Alt = new \Madlines\Validation\Field('field2', new \Madlines\Validation\Factory());
        $field2Alt->required('Field :field is required');
        foreach ($params['field2']['filters'] as $filter) {
            $field2Alt->addFilter($filter);
        }

        foreach ($params['field2']['rules'] as $ruleName => $rule) {
            $field2Alt->addRule($ruleName, $rule[0], $rule[1]);
        }

        $this->assertEquals($fieldsObjects['field2'], $field2Alt);
    }

    public function testForceError()
    {
        $validation = new \Madlines\Validation\Validation();
        $validation->field('field')
            ->forceError('Error message for :field');

        $passed = false;
        $messages = [];

        try {
            $validation->execute(
                [
                    'field' => 'Value'
                ]
            );
            $passed = true;
        } catch (\Madlines\Validation\ValidationException $e) {
            $passed = false;
            $messages = $e->getErrorMessages();
        }

        $this->assertFalse($passed);
        $this->assertEquals(
            [
                'field' => [
                    'Error message for field'
                ]
            ],
            $messages
        );
    }
}
