<?php

namespace Stratadox\Di;

use Closure;
use Exception;
use Stratadox\Di\Exception\InvalidServiceConfigurationException;
use Stratadox\Di\Exception\InvalidServiceException;
use Stratadox\Di\Exception\UndefinedServiceException;

class Container implements ContainerInterface
{
    /** @var object[] */
    protected $instances = [];

    /** @var callable[] */
    protected $factories = [];

    /**
     * @param string $name
     * @param string $type
     * @return object
     * @throws InvalidServiceException
     * @throws InvalidServiceConfigurationException
     * @throws UndefinedServiceException
     */
    public function get($name, $type = '') {
        if (!isset($this->instances[$name])) {
            if (!isset($this->factories[$name])) {
                throw new UndefinedServiceException(
                    sprintf('No service registered for %s', $name)
                );
            }
            try {
                $this->instances[$name] = $this->factories[$name]();
            } catch (Exception $e) {
                throw new InvalidServiceConfigurationException(
                    sprintf(
                        'Service %s was configured incorrectly and could not be created: %s',
                        $name,
                        $e->getMessage()
                    ),
                    0,
                    $e
                );
            }
        }
        $instance = $this->instances[$name];
        
        if (!is_object($instance)) {
            throw new InvalidServiceException(
                sprintf(
                    'Result of service %s (%s) is not an object',
                    $name,
                    gettype($instance)
                )
            );
        }
        if (($type !== '') && !($instance instanceof $type)) {
            throw new InvalidServiceException(
                sprintf(
                    'Instance of service %s (%s) is not an instance of %s',
                    $name,
                    get_class($instance),
                    $type
                )
            );
        }
        return $instance;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name) {
        return isset($this->factories[$name]);
    }

    /**
     * @param string $name
     * @param Closure $loader
     */
    public function set($name, Closure $loader) {
        $this->instances[$name] = null;
        $this->factories[$name] = $loader;
    }

    /**
     * @param array $services
     */
    public function setMany(array $services) {
        foreach ($services as $name => $loader) {
            $this->set($name, $loader);
        }
    }
}
