<?php
namespace Madlines\Validation\Filter;

class AppendBStub implements \Madlines\Validation\FilterInterface
{
    public function filter($input)
    {
        return $input . 'B';
    }
}
