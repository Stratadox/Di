<?php

namespace Stratadox\Di;

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
     * @return mixed
     * @throws \Exception
     */
    public function get($name, $interface = '') {
        if (!isset($this->instances[$name])) {
            if (!isset($this->factories[$name])) {
                throw new UndefinedServiceException('No service registered for '.$name);
            }
            $this->instances[$name] = $this->factories[$name]();
        }
        $instance = $this->instances[$name];
        if (!$instance instanceof $interface) {
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
     * @param callable $loader
     */
    public function set($name, callable $loader) {
        $this->instances[$name] = null;
        $this->factories[$name] = $loader;
    }

    /**
     * @param array $configuration as [name => callable]
     */
    public function configure(array $configuration) {
        foreach ($configuration as $name => $loader) {
            $this->set($name, $loader);
        }
    }
}