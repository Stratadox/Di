<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\ArrayAdapter;
use Stratadox\Di\Container;
use Stratadox\Di\Test\Stub\Foo;

/**
 * @covers \Stratadox\Di\ArrayAdapter
 */
class ArrayAdapterTest extends TestCase
{
    /** @test */
    function looking_up_a_service_through_the_container()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        $this->assertSame($container->get('foo'), $di['foo']);
    }

    /** @test */
    function registering_a_service_to_the_container()
    {
        $container = new Container();

        $di = new ArrayAdapter($container);
        $di['foo'] = function () {
            return new Foo();
        };

        $this->assertTrue($container->has('foo'));

        $this->assertSame($container->get('foo'), $di['foo']);
    }

    /** @test */
    function making_the_container_forget_a_service()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        unset($di['foo']);

        $this->assertFalse($container->has('foo'));
    }

    /** @test */
    function indicating_that_a_service_exists_in_the_container()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        $this->assertTrue(isset($di['foo']));
        $this->assertArrayHasKey('foo', $di);
    }

    /** @test */
    function indicating_that_a_service_does_not_exist_in_the_container()
    {
        $di = new ArrayAdapter(new Container());

        $this->assertFalse(isset($di['foo']));
        $this->assertArrayNotHasKey('foo', $di);
    }
}
