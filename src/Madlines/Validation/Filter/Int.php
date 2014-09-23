<?php
namespace Madlines\Validation\Filter;

use Madlines\Validation\FilterInterface;

/**
 * Madlines Validation Filter Int.
 * It casts all given input to integers.
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Int implements FilterInterface
{
    public function filter($input)
    {
        return (int) $input;
    }
}
