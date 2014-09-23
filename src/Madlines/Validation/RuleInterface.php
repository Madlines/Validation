<?php
namespace Madlines\Validation;

/**
 * Madlines Validation RuleInterface.
 * This is an interface which all Rules written for that mechanism should implement.
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
interface RuleInterface
{
    /**
     * Validates given input against the rule and provided params (if needed).
     * If verification passes - it returns false. Otherwise it returns error message.
     * String :field inside error message will be replaced with actual field's name ($field).
     * Same goes for every param (if supported by given rule).
     *
     * @param array  $input   - input data for given field to be verified
     * @param string $field   - the field's name
     * @param string $message - message to be returned if verification fails.
     * @param array  $params  - additional parameters
     *
     * @return mixed - boolean false (if no errors are found) or error message string.
     */
    public function validate($input, $field, $message, array $params = []);
}
