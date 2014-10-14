<?php
namespace Madlines\Validation\Rule;

use Madlines\Validation\RuleInterface;

/**
 * Madlines Validation Rule Email.
 * It verifies if given data is valid email address using built in PHP filters.
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Email implements RuleInterface
{

    public function validate($input, $field, $message, array $params = [])
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return str_replace(':field', $field, $message);
    }
}
