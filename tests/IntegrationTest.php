<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\ArrayAdapter;
use Stratadox\Di\AutoWiring;
use Stratadox\Di\DependencyContainer;
use Stratadox\Di\Test\Stub\Baz;
use Stratadox\Di\Test\Stub\Foo;

/**
 * @coversNothing
 */
class IntegrationTest extends TestCase
{
    /** @test */
    function autowiring_array_syntax_container()
    {
        $container = new ArrayAdapter(AutoWiring::the(new DependencyContainer));

        $this->assertInstanceOf(Baz::class, $container[Baz::class]);
    }

    /** @test */
    function dependencies_are_put_in_the_container()
    {
        $original = new DependencyContainer;
        $container = new ArrayAdapter(AutoWiring::the($original));

        $container[Baz::class]; // Magic

        $this->assertInstanceOf(Foo::class, $original->get(Foo::class));
    }
}
