<?php

namespace Stratadox\Di\Exception;

use Exception;

class InvalidFactory extends InvalidServiceDefinition
{
    public static function threwException($service, Exception $exception)
    {
        return new static(sprintf(
            'Service `%s` was configured incorrectly and could not be created: %s',
            $service,
            $exception->getMessage()
        ), 0, $exception);
    }
}
