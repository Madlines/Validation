<?php
namespace Madlines\Validation\Filter;

require_once __DIR__ . '/../../../../src/Madlines/Validation/FilterInterface.php';
require_once __DIR__ . '/../../../../src/Madlines/Validation/Filter/Int.php';

class IntTest extends \PHPUnit_Framework_TestCase
{
    public function filterDataProvider()
    {
        return [
            [3, 3],
            ['3', 3],
            ['3a', 3],
            ['test', 0],
            [5.4343434, 5],
            ['8.99293294', 8]
        ];
    }

    /**
     * @dataProvider filterDataProvider
     */
    public function testFilter($input, $output)
    {
        $filter = new \Madlines\Validation\Filter\Int();
        $filtered = $filter->filter($input);
        $this->assertEquals($filtered, $output);
    }
}
