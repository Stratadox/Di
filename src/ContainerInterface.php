<?php

namespace Stratadox\Di;

use Closure;

interface ContainerInterface
{
    /**
     * @param string $service
     * @param Closure $factory
     * @param bool $cache
     */
    public function set(string $service, Closure $factory, bool $cache = true);

    /**
     * @param string $service
     * @param string $type
     * @return mixed
     */
    public function get($service, string $type = '');

    /**
     * @param string $service
     * @return boolean
     */
    public function has($service) : bool;

    /**
     * @param string $service
     */
    public function forget(string $service);
}
