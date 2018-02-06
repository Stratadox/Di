<?php

declare(strict_types=1);

namespace Stratadox\Di;

use Psr\Container\ContainerExceptionInterface;
use Throwable;

interface InvalidServiceDefinition extends ContainerExceptionInterface, Throwable
{
}
