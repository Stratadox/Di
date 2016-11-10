<?php

namespace Stratadox\Di\Test\Asset;

use Stratadox\Di\ContainerInterface;

class Qux
{
    protected $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function doSomethingUseful()
    {
        $foo = $this->di->get('barbaz', BarInterface::class);
        return $foo->doSomethingUseful();
    }
}