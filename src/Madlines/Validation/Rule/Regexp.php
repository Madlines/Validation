<?php
namespace Madlines\Validation\Rule;

use Madlines\Validation\RuleInterface;

/**
 * Madlines Validation Rule Regexp.
 * It verifies if given data matches given pattern.
 * You should pass the regexp key 'regexp' inside params array
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Regexp implements RuleInterface
{

    public function validate($input, $field, $message, array $params = [])
    {
        if (!isset($params['regexp'])) {
            throw new \RuntimeException(
                'You have to specify a regexp param for "regexp" rule'
            );
        }

        $regexp = $params['regexp'];
        if (preg_match($regexp, $input)) {
            return false;
        }

        return str_replace(':field', $field, $message);
    }
}
