<?php

namespace Stratadox\Di;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Stratadox\Di\Exception\DependenciesCannotBeCircular;
use Stratadox\Di\Exception\InvalidFactory;
use Stratadox\Di\Exception\InvalidServiceType;
use Stratadox\Di\Exception\ServiceNotFound;
use Throwable;

class Container implements ContainerInterface, PsrContainerInterface
{
    protected $services = [];
    protected $factories = [];
    protected $useCache = [];
    protected $currentlyResolving = [];

    /**
     * @throws InvalidFactory
     * @throws ServiceNotFound
     * @throws InvalidServiceType
     */
    public function get($name, string $type = '')
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

    public function has($name) : bool
    {
        return isset($this->factories[$name]);
    }

    public function set(string $name, Closure $factory, bool $cache = true)
    {
        $this->services[$name] = null;
        $this->factories[$name] = $factory;
        $this->useCache[$name] = (bool) $cache;
    }

    public function forget(string $name)
    {
        unset(
            $this->factories[$name],
            $this->services[$name],
            $this->useCache[$name]
        );
    }

    /**
     * @throws InvalidFactory
     * @throws DependenciesCannotBeCircular
     * @throws ServiceNotFound
     */
    protected function loadService(string $name)
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
        } catch (Throwable $e) {
            throw InvalidFactory::threwException($name, $e);
        }
        unset($this->currentlyResolving[$name]);
        return $service;
    }
}
