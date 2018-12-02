<?php declare(strict_types=1);

namespace Stratadox\Di;

use InvalidArgumentException as InvalidArgument;
use ReflectionType;

final class CannotAutoWireBuiltInTypes extends InvalidArgument implements InvalidServiceDefinition
{
    public static function cannotResolve(ReflectionType $type): InvalidServiceDefinition
    {
        return new self(sprintf(
            'Cannot autowire the %s argument.',
            (string) $type
        ));
    }
}
