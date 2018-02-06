<?php

declare(strict_types=1);

namespace Stratadox\Di;

use RuntimeException;
use function sprintf;
use Throwable;

final class InvalidFactory extends RuntimeException implements InvalidServiceDefinition
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
