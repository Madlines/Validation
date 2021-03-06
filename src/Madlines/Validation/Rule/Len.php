<?php
namespace Madlines\Validation\Rule;

use Madlines\Validation\RuleInterface;

/**
 * Madlines Validation Rule Len.
 * It verifies if given data length is withing the given range.
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Len implements RuleInterface
{

    public function validate($input, $field, $message, array $params = [])
    {

        if (!isset($params['min']) || !isset($params['max'])) {
            throw new \LogicException(
                'You have to specify min and max params for "len" rule'
            );
        }

        $len = strlen($input);
        if ($len >= $params['min'] && $len <= $params['max']) {
            return false;
        }

        return str_replace(
            [':field', ':min', ':max'],
            [$field, $params['min'], $params['max']],
            $message
        );
    }
}
