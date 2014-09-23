<?php
namespace Madlines\Validation;

/**
 * Madlines Validation Exception
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class ValidationException extends \Exception
{
    /**
     * @var array - list of fields containing arrays of validation errors
     */
    protected $errorMessages = [];

    /**
     * Setter for $errorMessages.
     *
     * @param array $messages - data to be stored as $errorMessages
     *
     * @return self
     */
    public function setErrorMessages($messages)
    {
        $this->errorMessages = $messages;

        return $this;
    }

    /**
     * Getter for $errorMessages.
     *
     * @return array - list of fields containing arrays of validation errors
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
}
