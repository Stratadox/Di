<?php

declare(strict_types=1);

namespace Stratadox\Di\Exception;

use RuntimeException;
use function sprintf;

class InvalidServiceType extends RuntimeException implements InvalidServiceDefinition
{
    public static function serviceIsNotOfType(
        string $serviceName,
        string $expectedType
    ) : InvalidServiceDefinition
    {
        return new static(sprintf(
            'Service %s is not of type %s',
            $serviceName,
            $expectedType
        ));
    }
}
