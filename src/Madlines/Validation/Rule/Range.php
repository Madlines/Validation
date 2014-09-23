<?php
namespace Madlines\Validation\Rule;

use Madlines\Validation\RuleInterface;

/**
 * Madlines Validation Rule Range.
 * It verifies if given number is withing the given range.
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Range implements RuleInterface
{

    public function validate($input, $field, $message, array $params = [])
    {

        if (!isset($params['min']) || !isset($params['max'])) {
            throw new \RuntimeException(
                'You have to specify min and max params for "range" rule'
            );
        }

        if ($input >= $params['min'] && $input <= $params['max']) {
            return false;
        }

        return str_replace(
            [':field', ':min', ':max'],
            [$field, $params['min'], $params['max']],
            $message
        );
    }
}
