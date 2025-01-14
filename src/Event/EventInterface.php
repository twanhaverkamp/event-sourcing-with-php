<?php

namespace TwanHaverkamp\EventSourcingWithPhp\Event;

use DateTimeInterface;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId\AggregateRootIdInterface;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception\EventConstructionFailedException;

interface EventInterface
{
    public function getAggregateRootId(): AggregateRootIdInterface;

    /**
     * @param array<string, mixed> $payload
     *
     * @throws EventConstructionFailedException
     */
    public static function fromPayload(
        AggregateRootIdInterface $aggregateRootId,
        array $payload,
        DateTimeInterface $recordedAt,
    ): self;

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array;

    public function getRecordedAt(): DateTimeInterface;
}
