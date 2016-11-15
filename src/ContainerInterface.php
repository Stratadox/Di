<?php

namespace Stratadox\Di;

use Closure;

interface ContainerInterface
{
    public function configure(array $configuration);
    public function set($name, Closure $loader);
    public function get($name, $interface = '');
    public function has($name);
}
