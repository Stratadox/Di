<?php

namespace Stratadox\Di\Exception;

use Throwable;

class InvalidFactory extends InvalidServiceDefinition
{
    public static function threwException(
        string $serviceName,
        Throwable $exception
    ) : Throwable
    {
        return new static(sprintf(
            'Service `%s` was configured incorrectly and could not be created: %s',
            $serviceName,
            $exception->getMessage()
        ), 0, $exception);
    }
}
