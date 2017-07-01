<?php

namespace Stratadox\Di\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class ServiceNotFound
    extends RuntimeException
    implements NotFoundExceptionInterface
{
    public static function noServiceNamed($name)
    {
        return new static(sprintf(
            'No service registered for %s',
            $name
        ));
    }
}
