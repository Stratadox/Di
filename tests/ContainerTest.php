<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use Exception;
use PHPUnit\Framework\TestCase;
use Stratadox\Di\Container;
use Stratadox\Di\Test\Stub\Bar;
use Stratadox\Di\Test\Stub\BarInterface;
use Stratadox\Di\Test\Stub\Baz;
use Stratadox\Di\Test\Stub\Foo;
use Throwable;

/**
 * @covers \Stratadox\Di\Container
 */
class ContainerTest extends TestCase
{
    /** @test */
    function indicating_that_a_service_is_known()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertTrue($di->has('foo'));
    }

    /** @test */
    function indicating_that_a_service_is_not_known()
    {
        $di = new Container();
        $this->assertFalse($di->has('foo'));
    }

    /** @test */
    function looking_up_a_registered_service()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });

        $foo = $di->get('foo');

        $this->assertInstanceOf(Foo::class, $foo);
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
    function looking_up_a_service_with_an_interface_constraint()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        });

        $bar = $di->get('bar', BarInterface::class);

        $this->assertInstanceOf(BarInterface::class, $bar);
    }

    /** @test */
    function looking_up_a_service_with_a_scalar_constraint()
    {
        $di = new Container();
        $di->set('string', function () {
            return 'Hello world!';
        });

        $this->assertSame('Hello world!', $di->get('string', 'string'));
    }

    /** @test */
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

    /** @test */
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

    /** @test */
    function allowing_a_factory_to_produce_the_container_it_is_in()
    {
        $di = new Container();
        $di->set('di', function () use ($di) {
            return $di;
        });
        $this->assertSame($di, $di->get('di'));
    }

    /** @test */
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
