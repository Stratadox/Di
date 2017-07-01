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
    public function get($serviceName, string $type = '')
    {
        if (!isset($this->services[$serviceName]) || !$this->useCache[$serviceName]) {
            $this->services[$serviceName] = $this->loadService($serviceName);
        }
        $service = $this->services[$serviceName];
        if ($type === '') {
            return $service;
        }
        if ($type === gettype($service)) {
            return $service;
        }
        if ($service instanceof $type) {
            return $service;
        }
        throw InvalidServiceType::serviceIsNotOfType($serviceName, $type);
    }

    public function has($serviceName) : bool
    {
        return isset($this->factories[$serviceName]);
    }

    public function set(
        string $serviceName,
        Closure $factory,
        bool $useCache = true
    ) {
        $this->services[$serviceName] = null;
        $this->factories[$serviceName] = $factory;
        $this->useCache[$serviceName] = $useCache;
    }

    public function forget(string $serviceName)
    {
        unset(
            $this->factories[$serviceName],
            $this->services[$serviceName],
            $this->useCache[$serviceName]
        );
    }

    /**
     * @throws InvalidFactory
     * @throws DependenciesCannotBeCircular
     * @throws ServiceNotFound
     */
    protected function loadService(string $serviceName)
    {
        if (!isset($this->factories[$serviceName])) {
            throw ServiceNotFound::noServiceNamed($serviceName);
        }
        if (isset($this->currentlyResolving[$serviceName])) {
            throw DependenciesCannotBeCircular::loopDetectedIn($serviceName);
        }
        $this->currentlyResolving[$serviceName] = true;
        try {
            $service = $this->factories[$serviceName]();
        } catch (Throwable $exception) {
            throw InvalidFactory::threwException($serviceName, $exception);
        }
        unset($this->currentlyResolving[$serviceName]);
        return $service;
    }
}
