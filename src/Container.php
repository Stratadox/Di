<?php

namespace Stratadox\Di;

use Closure;
use Exception;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Stratadox\Di\Exception\CircularDependencyException;
use Stratadox\Di\Exception\InvalidFactoryException;
use Stratadox\Di\Exception\InvalidServiceException;
use Stratadox\Di\Exception\UndefinedServiceException;

class Container implements ContainerInterface, PsrContainerInterface
{
    /** @var mixed[] */
    protected $services = [];

    /** @var Closure[] */
    protected $factories = [];
    
    /** @var bool[] */
    protected $useCache = [];

    /** @var bool[] */
    protected $currentlyResolving = [];

    /**
     * @param string $name
     * @param string $type = ''
     * @return mixed
     * @throws InvalidFactoryException
     * @throws InvalidServiceException
     * @throws UndefinedServiceException
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
        throw new InvalidServiceException(sprintf(
            'Service %s is not of type %s',
            $name,
            $type
        ));
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
            $this->useCache[$name],
            $this->currentlyResolving[$name]
        );
    }

    /**
     * @param string $name
     * @return mixed
     * @throws InvalidFactoryException
     * @throws UndefinedServiceException
     */
    protected function loadService($name)
    {
        if (!isset($this->factories[$name])) {
            throw new UndefinedServiceException(sprintf(
                'No service registered for %s',
                $name
            ));
        }
        if (isset($this->currentlyResolving[$name])) {
            throw new CircularDependencyException(sprintf(
                'Circular dependency detected for %s',
                $name
            ));
        }
        $this->currentlyResolving[$name] = true;

        try {
            $service = $this->factories[$name]();
        } catch (Exception $e) {
            throw new InvalidFactoryException(sprintf(
                'Service %s was configured incorrectly and could not be created: %s',
                $name,
                $e->getMessage()
            ), 0, $e);
        }
        unset($this->currentlyResolving[$name]);
        return $service;
    }
}
