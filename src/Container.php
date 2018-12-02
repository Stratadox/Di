<?php

namespace Stratadox\Di;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Container\NotFoundExceptionInterface as NotFound;

interface Container extends PsrContainerInterface
{
    /**
     * Register a service to the container.
     *
     * @param string $service   The name of the service to register
     * @param Closure $factory  The function that produces the service
     * @param bool $cache       Whether or nor to cache the service
     *
     * @return void
     */
    public function set(string $service, Closure $factory, bool $cache = true): void;

    /**
     * Retrieve a service from the container.
     *
     * @param string $service   The name of the service to retrieve
     *
     * @return mixed            The service object
     *
     * @throws InvalidServiceDefinition
     * @throws NotFound
     */
    public function get($service);

    /**
     * Check whether a service is registered.
     *
     * @param string $service   The name of the service to check for
     *
     * @return boolean          Whether or not the service exists
     */
    public function has($service): bool;

    /**
     * Remove a service from the container.
     *
     * @param string $service   The name of the service to remove
     *
     * @return void
     */
    public function forget(string $service): void;
}
