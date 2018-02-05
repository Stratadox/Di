<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use Exception;
use PHPUnit\Framework\TestCase;
use Stratadox\Di\AutoWiring;
use Stratadox\Di\Container;
use Stratadox\Di\ContainerInterface;
use Stratadox\Di\Test\Stub\AnotherFoo;
use Stratadox\Di\Test\Stub\Bar;
use Stratadox\Di\Test\Stub\BarInterface;
use Stratadox\Di\Test\Stub\Baz;
use Stratadox\Di\Test\Stub\Foo;
use Stratadox\Di\Test\Stub\FooBar;
use Stratadox\Di\Test\Stub\FooInterface;

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

    /** @scenario */
    function retrieving_a_service_that_depends_on_two_interfaces()
    {
        $container = new Container;
        $autoWiring = AutoWiring::the($container)
             ->link(FooInterface::class, Foo::class)
             ->link(BarInterface::class, Bar::class);

        $fooBar = $autoWiring->get(FooBar::class);

        $this->assertSame($fooBar->foo(), $container->get(Foo::class));
        $this->assertSame($fooBar->bar(), $container->get(Bar::class));
    }

    /** @scenario */
    function overwriting_an_interface_link()
    {
        $container = new Container;
        $autoWiring = AutoWiring::the($container)
            ->link(FooInterface::class, Foo::class)
            ->link(BarInterface::class, Bar::class)
            ->link(FooInterface::class, AnotherFoo::class);

        $fooBar = $autoWiring->get(FooBar::class);

        $this->assertSame($fooBar->foo(), $container->get(AnotherFoo::class));
        $this->assertSame($fooBar->bar(), $container->get(Bar::class));
    }

    /**
     * @scenario
     * @dataProvider classes
     * @param string $class The class to check
     */
    function having_existing_classes(string $class)
    {
        $this->assertTrue(AutoWiring::the(new Container)->has($class));
    }

    /**
     * @scenario
     * @dataProvider notClasses
     * @param string $notAClass The class to check
     */
    function not_having_non_existing_classes(string $notAClass)
    {
        $this->assertFalse(AutoWiring::the(new Container)->has($notAClass));
    }

    /**
     * @scenario
     * @dataProvider interfaces
     * @param string $interface The interface to check
     */
    function having_existing_interfaces(string $interface)
    {
        $this->assertTrue(AutoWiring::the(new Container)->has($interface));
    }

    public function classes() : array
    {
        return [
            'Foo' => [Foo::class],
            'Bar' => [Bar::class],
            'Baz' => [Baz::class],
            'FooBar' => [FooBar::class],
            'AutoWiring' => [AutoWiring::class],
            'Exception' => [Exception::class],
        ];
    }

    public function interfaces() : array
    {
        return [
            'BarInterface' => [BarInterface::class],
            'FooInterface' => [FooInterface::class],
            'ContainerInterface' => [ContainerInterface::class],
        ];
    }

    public function notClasses() : array
    {
        return [
            'NotFoo' => [Foo::class.'ButNotReally'],
            'NotBar' => [Bar::class.'ButNotReally'],
            'NotBaz' => [Baz::class.'ButNotReally'],
            'NotFooBar' => [FooBar::class.'ButNotReally'],
            'NotAutoWiring' => [AutoWiring::class.'ButNotReally'],
            'NotException' => [__NAMESPACE__.Exception::class.'ButNotReally'],
        ];
    }
}
