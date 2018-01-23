<?php

namespace Stratadox\Di\Test;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Stratadox\Di\Container;
use Stratadox\Di\Exception\InvalidFactory;
use Stratadox\Di\Exception\InvalidServiceType;
use Stratadox\Di\Exception\ServiceNotFound;
use Stratadox\Di\Test\Stub\Bar;
use Stratadox\Di\Test\Stub\BarInterface;
use Stratadox\Di\Test\Stub\Baz;
use Stratadox\Di\Test\Stub\Foo;
use Throwable;

class ContainerTest extends TestCase
{
    /** @scenario */
    function indicating_that_a_service_is_known()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertTrue($di->has('foo'));
    }

    /** @scenario */
    function indicating_that_a_service_is_not_known()
    {
        $di = new Container();
        $this->assertFalse($di->has('foo'));
    }

    /** @scenario */
    function looking_up_a_registered_service()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });

        $foo = $di->get('foo');

        $this->assertInstanceOf(Foo::class, $foo);
    }

    /** @scenario */
    function looking_up_multiple_registered_services()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });
        $di->set('bar', function () {
            return new Bar();
        });

        $this->assertInstanceOf(Foo::class, $di->get('foo'));
        $this->assertInstanceOf(Bar::class, $di->get('bar'));
    }

    /** @scenario */
    function overriding_a_previously_registered_service()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Bar();
        });
        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertInstanceOf(Foo::class, $di->get('foo'));
    }

    /** @scenario */
    function composing_services_through_the_container()
    {
        $di = new Container();
        $di->set('baz', function () use ($di) {
            return new Baz($di->get('foo'));
        });
        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertInstanceOf(Foo::class, $di->get('baz')->getFoo());
    }

    /** @scenario */
    function caching_composite_services()
    {
        $di = new Container();
        $di->set('baz', function () use ($di) {
            return new Baz($di->get('foo'));
        });
        $di->set('foo', function () {
            return new Foo();
        });

        $baz1 = $di->get('baz');

        // Override collaborator 'foo' with new service
        $di->set('foo', function () {
            return new Bar();
        });

        // The override should not have any effect on the 'baz' service
        $baz2 = $di->get('baz');

        $this->assertSame($baz1, $baz2);
    }

    /** @scenario */
    function throwing_an_exception_when_a_factory_is_invalid()
    {
        $di = new Container();
        $di->set('baz', function () use ($di) {
            return new Baz($di->get('foo', Foo::class));
        });
        $di->set('foo', function () {
            return new Bar();
        });

        // Invalid because service 'foo' has a Bar, not a Foo
        $this->expectException(InvalidFactory::class);
        $di->get('baz');
    }

    /** @scenario */
    function throwing_an_exception_when_a_service_does_not_exist()
    {
        $di = new Container();

        $this->expectException(ServiceNotFound::class);
        $di->get('foo');
    }

    /** @scenario */
    function using_the_psr_interface_when_a_service_does_not_exist()
    {
        $di = new Container();

        $this->expectException(NotFoundExceptionInterface::class);
        $di->get('foo');
    }

    /** @scenario */
    function caching_the_instances_for_future_use()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        });

        $bar1 = $di->get('bar');
        $bar2 = $di->get('bar');

        $this->assertSame($bar1, $bar2);
    }

    /** @scenario */
    function not_caching_the_instances_that_have_caching_disabled()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        }, false);

        $bar1 = $di->get('bar');
        $bar2 = $di->get('bar');

        $this->assertNotSame($bar1, $bar2);
    }

    /** @scenario */
    function looking_up_a_service_with_an_interface_constraint()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        });

        $bar = $di->get('bar', BarInterface::class);

        $this->assertInstanceOf(BarInterface::class, $bar);
    }

    /** @scenario */
    function throwing_an_exception_when_an_interface_constraint_is_not_met()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        });

        $this->expectException(InvalidServiceType::class);
        $di->get('bar', Foo::class);
    }

    /** @scenario */
    function looking_up_a_service_with_a_scalar_constraint()
    {
        $di = new Container();
        $di->set('string', function () {
            return 'Hello world!';
        });

        $this->assertSame('Hello world!', $di->get('string', 'string'));
    }

    /** @scenario */
    function throwing_an_exception_when_a_scalar_constraint_is_not_met()
    {
        $di = new Container();
        $di->set('string', function () {
            return 'Hello world!';
        });

        $this->expectException(InvalidServiceType::class);
        $di->get('string', 'double');
    }

    /** @scenario */
    function indicating_that_a_forgotten_service_does_not_exist_anymore()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertTrue($di->has('foo'));

        $di->forget('foo');

        $this->assertFalse($di->has('foo'));
    }

    /** @scenario */
    function throwing_an_exception_when_a_factory_tries_to_infinitely_copy_itself()
    {
        $di = new Container();
        $di->set('foo', function () use ($di) {
            return $di->get('foo');
        });

        $this->expectException(InvalidFactory::class);
        $di->get('foo');
    }

    /** @scenario */
    function throwing_an_exception_when_factories_try_to_infinitely_copy_each_other()
    {
        $di = new Container();
        $di->set('foo', function () use ($di) {
            return $di->get('bar');
        });
        $di->set('bar', function () use ($di) {
            return $di->get('baz');
        });
        $di->set('baz', function () use ($di) {
            return $di->get('foo');
        });

        $this->expectException(InvalidFactory::class);
        $di->get('foo');
    }

    /** @scenario */
    function factories_that_forget_themselves_produce_once_and_quit_forever()
    {
        $di = new Container();
        $di->set('foo', function () use ($di) {
            $di->forget('foo');
            return 'Bye!';
        });

        $this->assertSame('Bye!', $di->get('foo'));
        $this->assertFalse($di->has('foo'));
    }

    /** @scenario */
    function allowing_a_factory_to_produce_the_container_it_is_in()
    {
        $di = new Container();
        $di->set('di', function () use ($di) {
            return $di;
        });
        $this->assertSame($di, $di->get('di'));
    }

    /** @scenario */
    function recovering_into_a_workable_state_after_encountering_an_exception()
    {
        $di = new Container();
        $di->set('foo', function () {
            throw new Exception();
        });

        try {
            $di->get('foo');
        } catch (Throwable $exception) {
            // As expected.
        }

        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertInstanceOf(Foo::class, $di->get('foo'));
    }
}
