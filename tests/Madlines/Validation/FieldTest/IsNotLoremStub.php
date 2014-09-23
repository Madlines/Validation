<?php
namespace Madlines\Validation\Rule;

class IsNotLoremStub implements \Madlines\Validation\RuleInterface
{
    public function validate($input, $field, $message, array $params = [])
    {
        if ('lorem' != $input) {
            return false;
        }

        return str_replace(':field', $field, $message);
    }
}
