<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\AutoWiring;
use Stratadox\Di\Container;
use Stratadox\Di\InvalidServiceType;
use Stratadox\Di\Test\Stub\Bar;
use Stratadox\Di\Test\Stub\FooInterface;

/**
 * @covers \Stratadox\Di\AutoWiring
 * @covers \Stratadox\Di\InvalidServiceType
 */
class InvalidServiceTypeTest extends TestCase
{
    /** @test */
    function throwing_an_exception_when_an_interface_constraint_is_not_met()
    {
        $this->expectException(InvalidServiceType::class);
        $this->expectExceptionMessage(
            'Service '.Bar::class.' is not of type '.FooInterface::class
        );
        AutoWiring::the(new Container)
            ->link(FooInterface::class, Bar::class);
    }
}
