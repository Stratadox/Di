<?php

namespace Stratadox\Di\Exception;

use Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends InvalidFactoryException implements ContainerExceptionInterface
{
}
