<?php
namespace Madlines\Validation;

/**
 * Madlines Validation Factory.
 * Instance of that class is meant to create all objects within current namespace.
 *
 * @author  Aleksander Ciesiolkiewicz <a.ciesiolkiewicz@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Factory
{

    /**
     * Create an object
     *
     * @param string $className - name of class of the object to be created
     * @param array  $args      - array of constructor's arguments
     *
     * @return mixed - created object
     */
    public function create($className, array $args = [])
    {
        $reflect  = new \ReflectionClass(__NAMESPACE__ . '\\' . $className);
        $instance = $reflect->newInstanceArgs($args);

        return $instance;
    }
}
