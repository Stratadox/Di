<?php

namespace Stratadox\Di;

use Psr\Container\ContainerExceptionInterface;
use Throwable;

interface InvalidServiceDefinition extends ContainerExceptionInterface, Throwable
{
}
