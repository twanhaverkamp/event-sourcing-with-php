<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Aggregate\Exception;

use InvalidArgumentException;

class AggregateRootIdConstructionFailedException extends InvalidArgumentException
{
}
