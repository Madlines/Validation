<?php
namespace Madlines\Validation\Rule;

class IsFoobarStub implements \Madlines\Validation\RuleInterface
{
    public function validate($input, $field, $message, array $params = [])
    {
        if ('foobar' == $input) {
            return false;
        }

        return str_replace(':field', $field, $message);
    }
}
