<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\Container;
use Stratadox\Di\Exception\InvalidServiceType;
use Stratadox\Di\Test\Stub\Bar;
use Stratadox\Di\Test\Stub\Foo;

/**
 * @covers \Stratadox\Di\Container
 * @covers \Stratadox\Di\Exception\InvalidServiceType
 */
class InvalidServiceTypeTest extends TestCase
{
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
    function throwing_an_exception_when_an_interface_constraint_is_not_met()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        });

        $this->expectException(InvalidServiceType::class);
        $di->get('bar', Foo::class);
    }
}
