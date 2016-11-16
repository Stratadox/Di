<?php

namespace Stratadox\Di;

use Closure;

interface ContainerInterface
{
    public function setMany(array $services);
    public function set($name, Closure $loader);
    public function get($name, $type = '');
    public function has($name);
}
