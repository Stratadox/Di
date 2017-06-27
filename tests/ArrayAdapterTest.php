<?php

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\ArrayAdapter;
use Stratadox\Di\Container;
use Stratadox\Di\Test\Stub\Foo;

class ArrayAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeAbleToGetService()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        $this->assertSame($container->get('foo'), $di['foo']);
    }

    /**
     * @test
     */
    public function shouldBeAbleToSetService()
    {
        $container = new Container();

        $di = new ArrayAdapter($container);
        $di['foo'] = function () {
            return new Foo();
        };

        $this->assertTrue($container->has('foo'));

        $this->assertSame($container->get('foo'), $di['foo']);
    }

    /**
     * @test
     */
    public function shouldBeAbleToUnsetServices()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        unset($di['foo']);

        $this->assertFalse($container->has('foo'));
    }

    /**
     * @test
     */
    public function shouldIndicateThatServiceExists()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        $this->assertTrue(isset($di['foo']));
        $this->assertArrayHasKey('foo', $di);
    }

    /**
     * @test
     */
    public function shouldIndicateThatServiceDoesNotExist()
    {
        $di = new ArrayAdapter(new Container());

        $this->assertFalse(isset($di['foo']));
        $this->assertArrayNotHasKey('foo', $di);
    }
}
