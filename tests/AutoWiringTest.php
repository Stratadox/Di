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
use Stratadox\Di\Test\Stub\FooBarQux;
use Stratadox\Di\Test\Stub\FooInterface;
use Stratadox\Di\Test\Stub\Qux;

/**
 * @covers \Stratadox\Di\AutoWiring
 */
class AutoWiringTest extends TestCase
{
    /** @test */
    function loading_a_service_without_explicitly_adding_it()
    {
        $container = AutoWiring::the(new Container);

        $this->assertInstanceOf(Foo::class, $container->get(Foo::class));
    }

    /** @test */
    function adding_the_auto_wired_service_to_the_container()
    {
        $container = new Container;
        $autoWiring = AutoWiring::the($container);

        $foo = $autoWiring->get(Foo::class);

        $this->assertSame($foo, $container->get(Foo::class));
    }

    /** @test */
    function adding_the_dependency_to_the_container_when_getting_the_service()
    {
        $container = new Container;
        $autoWiring = AutoWiring::the($container);

        $autoWiring->get(Baz::class);
        $this->assertTrue($container->has(Foo::class));
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
    function using_the_container_service_if_available()
    {
        $container = new Container;
        $container->set(Qux::class, function () {
            return new Qux('foo');
        });

        $autoWiring = AutoWiring::the($container)
            ->link(FooInterface::class, Foo::class)
            ->link(BarInterface::class, Bar::class);

        $fooBarQux = $autoWiring->get(FooBarQux::class);

        $this->assertSame($container->get(Qux::class), $fooBarQux->qux());
    }

    /** @test */
    function redirecting_set_calls_to_the_container()
    {
        $container = new Container;
        $autoWiring = AutoWiring::the($container);
        $autoWiring->set(Qux::class, function () {
            return new Qux('foo');
        });

        $this->assertTrue($container->has(Qux::class));
        $this->assertSame($container->get(Qux::class), $autoWiring->get(Qux::class));
    }

    /** @test */
    function redirecting_forget_calls_to_the_container()
    {
        $container = new Container;
        $container->set(Qux::class, function () {
            return new Qux('foo');
        });

        $autoWiring = AutoWiring::the($container);
        $autoWiring->forget(Qux::class);

        $this->assertFalse($container->has(Qux::class));
    }

    /**
     * @test
     * @dataProvider classes
     * @param string $class The class to check.
     */
    function having_existing_classes(string $class)
    {
        $this->assertTrue(AutoWiring::the(new Container)->has($class));
    }

    /**
     * @test
     * @dataProvider notClasses
     * @param string $notAClass The class to check.
     */
    function not_having_non_existing_classes(string $notAClass)
    {
        $this->assertFalse(AutoWiring::the(new Container)->has($notAClass));
    }

    /**
     * @test
     * @dataProvider interfaces
     * @param string $interface The interface to check.
     */
    function not_having_existing_but_unlinked_interfaces(string $interface)
    {
        $this->assertFalse(AutoWiring::the(new Container)->has($interface));
    }

    /**
     * @test
     * @dataProvider interfacesAndClasses
     * @param string $interface      The interface to check.
     * @param string $implementation The implementation to link.
     */
    function having_linked_interfaces(string $interface, string $implementation)
    {
        $container = AutoWiring::the(new Container)
            ->link($interface, $implementation);

        $this->assertTrue($container->has($interface));
    }

    /**
     * @test
     * @dataProvider notInterfaces
     * @param string $notAnInterface The interface to check.
     */
    function not_having_non_existing_interfaces(string $notAnInterface)
    {
        $this->assertFalse(AutoWiring::the(new Container)->has($notAnInterface));
    }

    /**
     * @test
     * @dataProvider notInterfaces
     * @param string $notAnInterface The interface to check.
     */
    function always_having_whatever_the_container_has(string $notAnInterface)
    {
        $container = new Container;
        $container->set($notAnInterface, function () { return "That's ok."; });
        $this->assertTrue(AutoWiring::the($container)->has($notAnInterface));
    }

    // Data Providers

    public function classes() : array
    {
        return [
            'Foo' => [Foo::class],
            'Bar' => [Bar::class],
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

    public function interfacesAndClasses() : array
    {
        return [
            'BarInterface' => [BarInterface::class, Bar::class],
            'FooInterface' => [FooInterface::class, Foo::class],
        ];
    }

    public function notClasses() : array
    {
        return [
            'Not Foo' => [Foo::class.'ButNotReally'],
            'Not Bar' => [Bar::class.'ButNotReally'],
            'Not Exception' => [__NAMESPACE__.Exception::class.'ButNotReally'],
        ];
    }

    public function notInterfaces() : array
    {
        return [
            'Not BarInterface' => [BarInterface::class.'ButNotReally'],
            'Not FooInterface' => [FooInterface::class.'ButNotReally'],
            'Not ContainerInterface' => [ContainerInterface::class.'ButNotReally'],
        ];
    }
}
