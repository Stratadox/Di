<?php

namespace Stratadox\Di;

use ArrayAccess;
use Closure;
use Stratadox\Di\Exception\InvalidFactory;
use Stratadox\Di\Exception\InvalidServiceType;
use Stratadox\Di\Exception\ServiceNotFound;

class ArrayAdapter implements ArrayAccess
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->container->has($offset);
    }

    /**
     * @param string $offset
     * @return mixed
     * @throws InvalidFactory
     * @throws InvalidServiceType
     * @throws ServiceNotFound
     */
    public function offsetGet($offset)
    {
        return $this->container->get($offset);
    }

    /**
     * @param string $offset
     * @param Closure $value
     */
    public function offsetSet($offset, $value)
    {
        $this->container->set($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->container->forget($offset);
    }
}
