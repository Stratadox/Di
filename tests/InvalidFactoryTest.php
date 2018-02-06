<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\Container;
use Stratadox\Di\Exception\InvalidFactory;
use Stratadox\Di\Test\Stub\Bar;
use Stratadox\Di\Test\Stub\Baz;
use Stratadox\Di\Test\Stub\Foo;

/**
 * @covers \Stratadox\Di\Container
 * @covers \Stratadox\Di\Exception\InvalidFactory
 * @covers \Stratadox\Di\Exception\DependenciesCannotBeCircular
 */
class InvalidFactoryTest extends TestCase
{
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
}
