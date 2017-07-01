<?php

namespace Stratadox\Di\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

abstract class InvalidServiceDefinition
    extends RuntimeException
    implements ContainerExceptionInterface
{
}
