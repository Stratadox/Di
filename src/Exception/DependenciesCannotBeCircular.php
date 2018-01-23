<?php

declare(strict_types=1);

namespace Stratadox\Di\Exception;

use RuntimeException;
use function sprintf;

class DependenciesCannotBeCircular extends RuntimeException implements InvalidServiceDefinition
{
    public static function loopDetectedIn($serviceName) : DependenciesCannotBeCircular
    {
        return new static(sprintf(
            'Circular dependency loop detected in factory `%s`.',
            $serviceName
        ));
    }
}
