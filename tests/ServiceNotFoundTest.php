<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Stratadox\Di\AutoWiring;
use Stratadox\Di\DependencyContainer;
use Stratadox\Di\ServiceNotFound;

/**
 * @covers \Stratadox\Di\AutoWiring
 * @covers \Stratadox\Di\DependencyContainer
 * @covers \Stratadox\Di\ServiceNotFound
 */
class ServiceNotFoundTest extends TestCase
{
    /** @test */
    function throwing_an_exception_when_a_service_does_not_exist()
    {
        $di = new DependencyContainer();

        $this->expectException(ServiceNotFound::class);
        $di->get('foo');
    }

    /** @test */
    function using_the_psr_interface_when_a_service_does_not_exist()
    {
        $di = new DependencyContainer();

        $this->expectException(NotFoundExceptionInterface::class);
        $di->get('foo');
    }

    /** @test */
    function throwing_an_exception_when_a_service_or_autowired_class_does_not_exist()
    {
        $di = AutoWiring::the(new DependencyContainer);

        $this->expectException(ServiceNotFound::class);
        $di->get(__NAMESPACE__.'foo');
    }
}
