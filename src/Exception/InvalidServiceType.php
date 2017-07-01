<?php

namespace Stratadox\Di\Exception;

use Throwable;

class InvalidServiceType extends InvalidFactory
{
    public static function serviceIsNotOfType(
        string $serviceName,
        string $expectedType
    ) : Throwable
    {
        return new static(sprintf(
            'Service %s is not of type %s',
            $serviceName,
            $expectedType
        ));
    }
}
