<?php

namespace Stratadox\Di;

use ArrayAccess;
use Closure;
use Stratadox\Di\Exception\InvalidFactoryException;
use Stratadox\Di\Exception\InvalidServiceException;
use Stratadox\Di\Exception\UndefinedServiceException;

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
     * @throws InvalidFactoryException
     * @throws InvalidServiceException
     * @throws UndefinedServiceException
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
