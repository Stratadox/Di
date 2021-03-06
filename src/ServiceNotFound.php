<?php declare(strict_types=1);

namespace Stratadox\Di;

use Psr\Container\NotFoundExceptionInterface as NotFound;
use RuntimeException;
use function sprintf;

final class ServiceNotFound extends RuntimeException implements NotFound
{
    public static function noServiceNamed(string $serviceName): NotFound
    {
        return new static(sprintf(
            'No service registered for %s',
            $serviceName
        ));
    }
}
