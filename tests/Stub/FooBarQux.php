<?php

declare(strict_types=1);

namespace Stratadox\Di\Test\Stub;

class FooBarQux
{
    private $foo;
    private $bar;
    private $qux;

    public function __construct(FooInterface $foo, BarInterface $bar, Qux $qux)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->qux = $qux;
    }

    public function foo() : FooInterface
    {
        return $this->foo;
    }

    public function bar() : BarInterface
    {
        return $this->bar;
    }

    public function qux() : Qux
    {
        return $this->qux;
    }
}
