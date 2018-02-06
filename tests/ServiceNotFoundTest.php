<?php

declare(strict_types=1);

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Stratadox\Di\Container;
use Stratadox\Di\Exception\ServiceNotFound;

/**
 * @covers \Stratadox\Di\Container
 * @covers \Stratadox\Di\Exception\ServiceNotFound
 */
class ServiceNotFoundTest extends TestCase
{
    /** @scenario */
    function throwing_an_exception_when_a_service_does_not_exist()
    {
        $di = new Container();

        $this->expectException(ServiceNotFound::class);
        $di->get('foo');
    }

    /** @scenario */
    function using_the_psr_interface_when_a_service_does_not_exist()
    {
        $di = new Container();

        $this->expectException(NotFoundExceptionInterface::class);
        $di->get('foo');
    }
}
