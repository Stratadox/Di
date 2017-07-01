<?php

namespace Stratadox\Di;

use Closure;
use Exception;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Stratadox\Di\Exception\DependenciesCannotBeCircular;
use Stratadox\Di\Exception\InvalidFactory;
use Stratadox\Di\Exception\InvalidServiceType;
use Stratadox\Di\Exception\ServiceNotFound;

class Container implements ContainerInterface, PsrContainerInterface
{
    protected $services = [];
    protected $factories = [];
    protected $useCache = [];
    protected $currentlyResolving = [];

    /**
     * @param string $name
     * @param string $type
     * @return mixed
     * @throws InvalidFactory
     * @throws ServiceNotFound
     * @throws InvalidServiceType
     */
    public function get($name, $type = '')
    {
        if (!isset($this->services[$name]) || !$this->useCache[$name]) {
            $this->services[$name] = $this->loadService($name);
        }
        $service = $this->services[$name];
        if ($type === '') {
            return $service;
        }
        if ($type === gettype($service)) {
            return $service;
        }
        if ($service instanceof $type) {
            return $service;
        }
        throw InvalidServiceType::serviceIsNotOfType($name, $type);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->factories[$name]);
    }

    /**
     * @param string $name
     * @param Closure $factory
     * @param bool $cache
     */
    public function set($name, Closure $factory, $cache = true)
    {
        $this->services[$name] = null;
        $this->factories[$name] = $factory;
        $this->useCache[$name] = (bool) $cache;
    }

    /**
     * @param string $name
     */
    public function forget($name)
    {
        unset(
            $this->factories[$name],
            $this->services[$name],
            $this->useCache[$name]
        );
    }

    /**
     * @param string $name
     * @return mixed
     * @throws InvalidFactory
     * @throws DependenciesCannotBeCircular
     * @throws ServiceNotFound
     */
    protected function loadService($name)
    {
        if (!isset($this->factories[$name])) {
            throw ServiceNotFound::noServiceNamed($name);
        }
        if (isset($this->currentlyResolving[$name])) {
            throw DependenciesCannotBeCircular::loopDetectedIn($name);
        }
        $this->currentlyResolving[$name] = true;

        try {
            $service = $this->factories[$name]();
        } catch (Exception $e) {
            throw InvalidFactory::threwException($name, $e);
        }
        unset($this->currentlyResolving[$name]);
        return $service;
    }
}
