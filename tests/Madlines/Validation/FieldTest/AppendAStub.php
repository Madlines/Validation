<?php
namespace Madlines\Validation\Filter;

class AppendAStub implements \Madlines\Validation\FilterInterface
{
    public function filter($input)
    {
        return $input . 'A';
    }
}
