<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\AutoWiring;
use Stratadox\Di\CannotResolveAbstractType;
use Stratadox\Di\DependencyContainer;
use Stratadox\Di\Test\Stub\AbstractFoo;
use Stratadox\Di\Test\Stub\FooInterface;

/**
 * @covers \Stratadox\Di\AutoWiring
 * @covers \Stratadox\Di\CannotResolveAbstractType
 */
class CannotResolveAbstractTypeTest extends TestCase
{
    /** @test */
    function throwing_an_exception_when_autowiring_an_unlinked_interface()
    {
        $container = AutoWiring::the(new DependencyContainer);

        $this->expectException(CannotResolveAbstractType::class);
        $this->expectExceptionMessage(
            'Cannot resolve the interface `'.FooInterface::class.'`. '.
            'Consider adding an AutoWire link.'
        );

        $container->get(FooInterface::class);
    }

    /** @test */
    function throwing_an_exception_when_autowiring_an_unlinked_abstract_class()
    {
        $container = AutoWiring::the(new DependencyContainer);

        $this->expectException(CannotResolveAbstractType::class);
        $this->expectExceptionMessage(
            'Cannot resolve the abstract class `'.AbstractFoo::class.'`. '.
            'Consider adding an AutoWire link.'
        );

        $container->get(AbstractFoo::class);
    }
}
