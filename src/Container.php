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
     * @param string $interface
     * @return object
     * @throws \Exception
     */
    public function get($name, $interface = '') {
        if (!isset($this->instances[$name])) {
            if (!isset($this->factories[$name])) {
                throw new UndefinedServiceException('No service registered for '.$name);
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
        if ($interface && !($instance instanceof $interface)) {
            throw new InvalidServiceException(sprintf('Instance of service %s (%s) does not implement %s', $name, get_class($instance), $interface));
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
     * @param $name
     * @param Closure $loader
     */
    public function set($name, Closure $loader) {
        $this->instances[$name] = null;
        $this->factories[$name] = $loader;
    }

    /**
     * @param array $configuration as [name => Closure]
     */
    public function setMany(array $configuration) {
        foreach ($configuration as $name => $loader) {
            $this->set($name, $loader);
        }
    }
}
