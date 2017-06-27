<?php

namespace Stratadox\Di\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class InvalidFactoryException extends RuntimeException implements ContainerExceptionInterface
{
}
