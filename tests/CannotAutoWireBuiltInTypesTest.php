<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\AutoWiring;
use Stratadox\Di\DependencyContainer;
use Stratadox\Di\CannotAutoWireBuiltInTypes;
use Stratadox\Di\Test\Stub\Qux;

/**
 * @covers \Stratadox\Di\AutoWiring
 * @covers \Stratadox\Di\CannotAutoWireBuiltInTypes
 */
class CannotAutoWireBuiltInTypesTest extends TestCase
{
    /** @test */
    function throwing_an_exception_when_trying_to_autowire_a_class_with_scalar_dependencies()
    {
        $container = AutoWiring::the(new DependencyContainer);

        $this->expectException(CannotAutoWireBuiltInTypes::class);
        $this->expectExceptionMessage(
            'Cannot autowire the string argument.'
        );

        $container->get(Qux::class);
    }
}
