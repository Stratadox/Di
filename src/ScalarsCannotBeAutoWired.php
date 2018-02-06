<?php

declare(strict_types=1);

namespace Stratadox\Di;

use InvalidArgumentException as InvalidArgument;

final class ScalarsCannotBeAutoWired extends InvalidArgument implements InvalidServiceDefinition
{
}
