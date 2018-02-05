<?php

declare(strict_types=1);

namespace Stratadox\Di\Test\Stub;

class FooBar
{
    private $foo;
    private $bar;

    public function __construct(FooInterface $foo, BarInterface $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function foo() : FooInterface
    {
        return $this->foo;
    }

    public function bar() : BarInterface
    {
        return $this->bar;
    }
}
