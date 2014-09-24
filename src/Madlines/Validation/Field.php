<?php
namespace Madlines\Validation;

/**
 * Madlines Validation Field.
 * All fields added to Validation are instances of Field.
 * Field can have different rules and filters set on itself.
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Field
{

    /**
     * @var array - array of rules
     */
    protected $rules = [];

    /**
     * @var array - array of filters
     */
    protected $filters = [];

    /**
     * @var string - name of the field
     */
    protected $name;

    /**
     * @var Factory - instance of class \Madlines\Validation\Factory responsible for creating all objects inside Validation.
     */
    protected $factory;

    /**
     * @var boolean - indicates if field is required or not
     */
    protected $required = false;

    /**
     * @var string - if field is required but empty - that message will be displayed
     */
    protected $requiredMessage = '';

    /**
     * @var boolean - indicates if field's value should be treated as improper even if it passes all set rules.
     */
    protected $forceError = false;

    /**
     * @var string - error message for forced error
     */
    protected $forceErrorMessage = '';

    /**
     * Constructor sets object's name and the factory instance.
     *
     * @param string  $name    - name of the field
     * @param Factory $factory - a Factory instance to be used for Rules and Filters objects
     */
    public function __construct($name, Factory $factory)
    {
        $this->name = $name;
        $this->factory = $factory;
    }

    /**
     * Get field's name.
     *
     * @return string - field's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add rule to the field with given params. Message can contain placeholders
     * for all params used inside the rule and for field's name (e.g. 'Field :field needs to be proper')
     *
     * @param string $name    - rule's name.
     * @param string $message - Message to be displayed if error occurs.
     * @param array  $params  - additional parameters
     *
     * @return self
     */
    public function addRule($name, $message, $params = [])
    {
        if (!isset($this->rules[$name])) {
            $this->rules[$name] = [
                'message' => $message,
                'params' => $params
            ];
        }

        return $this;
    }

    /**
     * Add filter to the field with given params.
     *
     * @param string $name - filter's name.
     *
     * @return self
     */
    public function addFilter($name)
    {
        if (!in_array($name, $this->filters)) {
            $this->filters[] = $name;
        }

        return $this;
    }

    /**
     * Filters given value against all filters set in order of setting.
     *
     * @param mixed $value - value to be filtered
     *
     * @return mixed - filtered input value
     */
    public function filter($value)
    {
        $filtered = $value;
        foreach ($this->filters as $filterName) {
            $className = 'Filter\\' . ucfirst($filterName);
            if (!class_exists(__NAMESPACE__ . '\\' . $className)) {
                throw new \RuntimeException(
                    'Class ' . $className . ' doesn\'t exist within current namespace'
                );
            }

            $filter = $this->factory->create($className);
            if (!($filter instanceof FilterInterface)) {
                throw new \RuntimeException(
                    'Class ' . $className . ' should implement FilterInterface'
                );
            }

            $filtered = $filter->filter($filtered);
        }

        return $filtered;
    }

    /**
     * Performs testing of given value against all rules set.
     *
     * @param mixed $value - value to be verified.
     *
     * @return mixed - boolean false if everything is fine, array of errors otherwise
     */
    public function validate($value)
    {
        if ($this->required) {
            if (null === $value || '' === $value) {
                return [
                    str_replace(':field', $this->name, $this->requiredMessage)
                ];
            }
        }

        $result = [];
        if ($this->forceError) {
            $result[] = str_replace(':field', $this->name, $this->forceErrorMessage);
        }

        foreach ($this->rules as $ruleName => $rule) {
            $className = 'Rule\\' . ucfirst($ruleName);
            if (!class_exists(__NAMESPACE__ . '\\' . $className)) {
                throw new \RuntimeException(
                    'Class ' . $className . ' doesn\'t exist within current namespace'
                );
            }

            $ruleObject = $this->factory->create($className);
            if (!($ruleObject instanceof RuleInterface)) {
                throw new \RuntimeException(
                    'Class ' . $className . ' should implement RuleInterface'
                );
            }

            $outcome = $ruleObject->validate($value, $this->name, $rule['message'], $rule['params']);
            if ($outcome) {
                $result[] = $outcome;
            }
        }

        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * Sets field to be required.
     * Error message can contain :field placeholder.
     *
     * @param string $message - error message to be displayed if required field's value is empty.
     *
     * @return self
     */
    public function required($message)
    {
        $this->required = true;
        $this->requiredMessage = $message;

        return $this;
    }

    /**
     * Forces error on field.
     * Error message can contain :field placeholder.
     * Unlike other rules - this will be triggered even if field's value is empty and unrequired.
     *
     * @param string $message - error message to be displayed.
     *
     * @return self
     */
    public function forceError($message)
    {
        $this->forceError = true;
        $this->forceErrorMessage = $message;

        return $this;
    }
}
