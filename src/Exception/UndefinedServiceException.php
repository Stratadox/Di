<?php

namespace Stratadox\Di\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class UndefinedServiceException extends RuntimeException implements NotFoundExceptionInterface
{
}
