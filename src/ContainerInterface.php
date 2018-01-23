<?php

declare(strict_types=1);

namespace Stratadox\Di;

use Closure;
use Stratadox\Di\Exception\InvalidServiceDefinition;
use Stratadox\Di\Exception\ServiceNotFound;

interface ContainerInterface
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
    public function set(string $service, Closure $factory, bool $cache = true);

    /**
     * Retrieve a service from the container.
     *
     * @param string $service   The name of the service to retrieve
     * @param string $type      The type (interface or scalar) requirement
     *
     * @return mixed            The service object
     *
     * @throws InvalidServiceDefinition
     * @throws ServiceNotFound
     */
    public function get($service, string $type = '');

    /**
     * Check whether a service is registered.
     *
     * @param string $service   The name of the service to check for
     *
     * @return boolean          Whether or not the service exists
     */
    public function has($service) : bool;

    /**
     * Remove a service from the container.
     *
     * @param string $service   The name of the service to remove
     *
     * @return void
     */
    public function forget(string $service);
}
