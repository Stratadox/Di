<?php

namespace Stratadox\Di\Exception;

class DependenciesCannotBeCircular extends InvalidFactory
{
    public static function loopDetectedIn($service)
    {
        return new static(sprintf(
            'Circular dependency loop detected in factory `%s`.',
            $service
        ));
    }
}
