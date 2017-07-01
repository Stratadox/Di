<?php

namespace Stratadox\Di;

use Closure;

interface ContainerInterface
{
    /**
     * @param string $serviceName
     * @param Closure $factory
     * @param bool $useCache
     */
    public function set(string $serviceName, Closure $factory, bool $useCache = true);

    /**
     * @param string $serviceName
     * @param string $type
     * @return mixed
     */
    public function get($serviceName, string $type = '');

    /**
     * @param string $serviceName
     * @return boolean
     */
    public function has($serviceName) : bool;

    /**
     * @param string $serviceName
     */
    public function forget(string $serviceName);
}
