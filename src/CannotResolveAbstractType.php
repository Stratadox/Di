<?php

declare(strict_types=1);

namespace Stratadox\Di;

use ReflectionClass;
use RuntimeException;
use function sprintf;

final class CannotResolveAbstractType extends RuntimeException implements InvalidServiceDefinition
{
    public static function noLinkDefinedFor(string $theAbstractType)
    {
        return new self(sprintf(
            'Cannot resolve the %s `%s`. Consider adding an AutoWire link.',
            self::typeOf(new ReflectionClass($theAbstractType)),
            $theAbstractType
        ));
    }

    private static function typeOf(ReflectionClass $class)
    {
        return $class->isInterface() ? 'interface' : 'abstract class';
    }
}
