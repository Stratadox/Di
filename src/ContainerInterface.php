<?php

namespace Stratadox\Di;

use Closure;

interface ContainerInterface
{
    public function set($name, Closure $factory);
    public function get($name, $type = '');
    public function has($name);
}
