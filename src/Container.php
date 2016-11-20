<?php

namespace Stratadox\Di;

use Closure;
use Exception;
use Stratadox\Di\Exception\InvalidFactoryException;
use Stratadox\Di\Exception\InvalidServiceException;
use Stratadox\Di\Exception\UndefinedServiceException;

class Container implements ContainerInterface
{
    /** @var mixed[] */
    protected $services = [];

    /** @var Closure[] */
    protected $factories = [];

    /**
     * @param string $name
     * @param string $type = ''
     * @return mixed
     * @throws InvalidFactoryException
     * @throws InvalidServiceException
     * @throws UndefinedServiceException
     */
    public function get($name, $type = '') {
        if (!isset($this->services[$name])) {
            if (!isset($this->factories[$name])) {
                throw new UndefinedServiceException(sprintf('No service registered for %s', $name));
            }
            try {
                $this->services[$name] = $this->factories[$name]();
            } catch (Exception $e) {
                throw new InvalidFactoryException(sprintf(
					'Service %s was configured incorrectly and could not be created: %s',
					$name,
					$e->getMessage()
				), 0, $e);
            }
        }
        $service = $this->services[$name];
        if ($type !== '' && gettype($service) !== $type && !($service instanceof $type)) {
            throw new InvalidServiceException(sprintf(
				'Service %s (%s) is not of type %s',
				$name,
				is_object($service) ? get_class($service) : gettype($service),
				$type
			));
        }
        return $service;
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
     * @param Closure $factory
     */
    public function set($name, Closure $factory) {
        $this->services[$name] = null;
        $this->factories[$name] = $factory;
    }

    /**
     * @param string $name
     */
    public function forget($name) {
        unset($this->factories[$name], $this->services[$name]);
    }
}
