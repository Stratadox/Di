<?php declare(strict_types=1);

namespace Stratadox\Di;

use ReflectionClass as Reflected;
use RuntimeException;
use function sprintf;

final class CannotResolveAbstractType extends RuntimeException implements InvalidServiceDefinition
{
    public static function noLinkDefinedFor(
        Reflected $theAbstractType
    ): InvalidServiceDefinition {
        return new self(sprintf(
            'Cannot resolve the %s `%s`. Consider adding an AutoWire link.',
            $theAbstractType->isInterface() ? 'interface' : 'abstract class',
            $theAbstractType->getName()
        ));
    }
}
