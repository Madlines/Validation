<?php
namespace Madlines\Validation;

require_once __DIR__ . '/../../../src/Madlines/Validation/ValidationException.php';

class ValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsException()
    {
        $exception = new \Madlines\Validation\ValidationException();
        $isException = $exception instanceof \Exception;
        $this->assertTrue($isException);
    }

    public function testSetAndGetErrorMessages()
    {
        $exception = new \Madlines\Validation\ValidationException();
        $reflection = new \ReflectionClass($exception);
        $messages = $reflection->getProperty('errorMessages');
        $messages->setAccessible(true);

        $this->assertEmpty($messages->getValue($exception));
        $messagesToAdd = [
            'foo' => [
                'message', 'anotherone'
            ]
        ];

        $exception->setErrorMessages($messagesToAdd);
        $this->assertEquals($messagesToAdd, $messages->getValue($exception));
        $this->assertEquals($messagesToAdd, $exception->getErrorMessages());
    }
}
