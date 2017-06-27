<?php

namespace Stratadox\Di;

use Closure;

interface ContainerInterface
{
    /**
     * @param string $name
     * @param Closure $factory
     * @param bool $cache
     */
    public function set($name, Closure $factory, $cache = true);

    /**
     * @param string $name
     * @param string $type
     * @return mixed
     */
    public function get($name, $type = '');

    /**
     * @param string $name
     * @return boolean
     */
    public function has($name);

    /**
     * @param string $name
     */
    public function forget($name);
}
