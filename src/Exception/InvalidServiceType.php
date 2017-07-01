<?php

namespace Stratadox\Di\Exception;

class InvalidServiceType extends InvalidFactory
{
    public static function serviceIsNotOfType($serviceName, $expectedType)
    {
        return new static(sprintf(
            'Service %s is not of type %s',
            $serviceName,
            $expectedType
        ));
    }
}
