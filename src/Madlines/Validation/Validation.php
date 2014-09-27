<?php
namespace Madlines\Validation;

/**
 * Madlines Validation
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Validation
{

    /**
     * @var array - array of fields added using method "field"
     */
    protected $fields = [];

    /**
     * @var Factory - instance of class \Madlines\Validation\Factory responsible for creating all objects inside Validation.
     */
    protected $factory;

    /**
     * Constructor can accept configuration array based on which it will preconfigure entire object.
     * It can be simply ommited.
     *
     * @param array $config - configuration array
     */
    public function __construct(array $config = [])
    {
        $this->factory = new Factory();
        foreach ($config as $fieldName => $field) {

            $required = (isset($field['required']) && $field['required'])
                ? $field['required']
                : null;

            $filters = isset($field['filters']) ? $field['filters'] : [];
            $rules = isset($field['rules']) ? $field['rules'] : [];
            $default = isset($field['default']) ? $field['default'] : null;

            if ($required) {
                $this->field($fieldName)->required($required);
            }
            
            $fieldObject = $this->field($fieldName);
            $fieldObject->setDefault($default);
            foreach ($filters as $filter) {
                $fieldObject->addFilter($filter);
            }

            foreach ($rules as $ruleName => $rule) {
                $rule = (array) $rule;
                $message = $rule[0];
                $params = isset($rule[1]) ? $rule[1] : [];
                $fieldObject->addRule($ruleName, $message, $params);
            }
        }
    }

    /**
     * Getter and setter for fields. If field of given name already exists - previous instance will be returned.
     * Otherwise new \Madlines\Validation\Field will be created and returned.
     *
     * @param string $name - name for the field
     *
     * @return Field
     */
    public function field($name)
    {
        if (!isset($this->fields[$name])) {
            $this->fields[$name] = $this->factory->create('Field', [$name, $this->factory]);
        }

        return $this->fields[$name];
    }

    /**
     * Execute filtering and validating process on given dataset.
     * It returns filtered data or throws an exception containing validation errors.
     * All fields set will be present on the output. If any key will be missing in input
     * array then it will be assumed to be null (by default) and processed as null.
     *
     * @param array $data - input data to be filtered and validated.
     *
     * @throws ValidationException - excpetion containing error messages.
     * @return array               - filtered data
     */
    public function execute(array $data)
    {
        $messages = [];
        $filtered = [];

        foreach ($this->fields as $name => $field) {
            if (!isset($data[$name])) {
                $data[$name] = $field->getDefault();
            }

            $filtered[$name] = $field->filter($data[$name]);
            $fieldMessages = $field->validate($filtered[$name]);
            if (!empty($fieldMessages)) {
                $messages[$name] = $field->validate($filtered[$name]);
            }
        }

        if (empty($messages)) {
            return $filtered;
        }

        $exception = new ValidationException('Validation Failed');
        $exception->setErrorMessages($messages);
        throw $exception;
    }
}
