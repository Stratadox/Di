<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\ArrayAdapter;
use Stratadox\Di\AutoWiring;
use Stratadox\Di\Container;
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
        $container = new ArrayAdapter(AutoWiring::the(new Container));

        $this->assertInstanceOf(Baz::class, $container[Baz::class]);
    }

    /** @test */
    function dependencies_are_put_in_the_container()
    {
        $original = new Container;
        $container = new ArrayAdapter(AutoWiring::the($original));

        $container[Baz::class]; // Magic

        $this->assertInstanceOf(Foo::class, $original->get(Foo::class));
    }
}
