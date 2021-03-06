<?php

namespace Stratadox\Di\Test\Stub;

class Baz
{
    /**
     * @var Foo
     */
    private $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return Foo
     */
    public function getFoo()
    {
        return $this->foo;
    }
}
