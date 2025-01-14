<?php

namespace TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;

use TwanHaverkamp\EventSourcingWithPhp\Aggregate\Exception;

interface AggregateRootIdInterface
{
    /**
     * @throws Exception\AggregateRootIdConstructionFailedException
     */
    public static function fromString(string $aggregateRootId): self;

    public function toString(): string;
}
