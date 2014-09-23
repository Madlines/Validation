<?php
namespace Madlines\Validation;

/**
 * Madlines Validation FilterInterface.
 * This is an interface which all filters written for that mechanism should implement.
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
interface FilterInterface
{

    /**
     * Filter input and return filtered version.
     *
     * @param mixed $input - any kind of entry data for
     *
     * @return mixed - filtered $input
     */
    public function filter($input);
}
