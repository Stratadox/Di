<?php

declare(strict_types=1);

namespace Stratadox\Di\Exception;

use Throwable;

class DependenciesCannotBeCircular extends InvalidFactory
{
    public static function loopDetectedIn($serviceName) : Throwable
    {
        return new static(sprintf(
            'Circular dependency loop detected in factory `%s`.',
            $serviceName
        ));
    }
}
