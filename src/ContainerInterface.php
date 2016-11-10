<?php

namespace Stratadox\Di;

interface ContainerInterface
{
    public function configure(array $configuration);
    public function set($name, callable $loader);
    public function get($name, $interface = '');
    public function has($name);
}