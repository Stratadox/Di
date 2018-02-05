<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\AutoWiring;
use Stratadox\Di\Container;
use Stratadox\Di\Test\Stub\Bar;
use Stratadox\Di\Test\Stub\BarInterface;
use Stratadox\Di\Test\Stub\Baz;
use Stratadox\Di\Test\Stub\Foo;

/**
 * @covers \Stratadox\Di\AutoWiring
 */
class AutoWiringTest extends TestCase
{
    /** @scenario */
    function loading_a_service_without_explicitly_adding_it()
    {
        $container = AutoWiring::the(new Container);

        $this->assertInstanceOf(Foo::class, $container->get(Foo::class));
    }

    /** @scenario */
    function adding_the_auto_wired_service_to_the_container()
    {
        $container = new Container;
        $autoWiring = AutoWiring::the($container);

        $foo = $autoWiring->get(Foo::class);

        $this->assertSame($foo, $container->get(Foo::class));
    }

    /** @scenario */
    function adding_the_dependency_to_the_container_when_getting_the_service()
    {
        $container = new Container;
        $autoWiring = AutoWiring::the($container);

        $autoWiring->get(Baz::class);
        $this->assertTrue($container->has(Foo::class));
    }

    /** @scenario */
    function using_the_service_from_the_underlying_container()
    {
        $container = new Container;
        $container->set(Foo::class, function () {
            return new Foo;
        });
        $foo = $container->get(Foo::class);

        $autoWiring = AutoWiring::the($container);

        $this->assertSame($foo, $autoWiring->get(Foo::class));
    }

    /** @scenario */
   function retrieving_the_linked_implementation_of_an_interface()
   {
       $container = new Container;
       $autoWiring = AutoWiring::the($container)
           ->link(BarInterface::class, Bar::class);

       $bar = $autoWiring->get(BarInterface::class);

       $this->assertInstanceOf(Bar::class, $bar);
       $this->assertSame($bar, $container->get(Bar::class));
       $this->assertSame($bar, $container->get(BarInterface::class));
   }
}