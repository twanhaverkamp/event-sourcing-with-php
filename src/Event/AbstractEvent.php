<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Event;

use DateTimeImmutable;
use DateTimeInterface;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId\AggregateRootIdInterface;

abstract readonly class AbstractEvent implements EventInterface
{
    public function __construct(
        protected AggregateRootIdInterface $aggregateRootId,
        protected DateTimeInterface $recordedAt = new DateTimeImmutable(),
    ) {
    }

    public function getAggregateRootId(): AggregateRootIdInterface
    {
        return $this->aggregateRootId;
    }

    public function getRecordedAt(): DateTimeInterface
    {
        return $this->recordedAt;
    }
}
