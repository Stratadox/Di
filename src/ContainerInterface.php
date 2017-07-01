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
    public function set(string $name, Closure $factory, bool $cache = true);

    /**
     * @param string $name
     * @param string $type
     * @return mixed
     */
    public function get($name, string $type = '');

    /**
     * @param string $name
     * @return boolean
     */
    public function has($name) : bool;

    /**
     * @param string $name
     */
    public function forget(string $name);
}
