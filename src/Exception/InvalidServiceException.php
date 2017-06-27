<?php

namespace Stratadox\Di\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class InvalidServiceException extends RuntimeException implements ContainerExceptionInterface
{
}
