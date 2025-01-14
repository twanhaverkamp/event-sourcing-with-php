<?php

namespace TwanHaverkamp\EventSourcingWithPhp\Aggregate;

use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId\AggregateRootIdInterface;
use TwanHaverkamp\EventSourcingWithPhp\Event\EventInterface;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception\EventNotSupportedException;

interface AggregateInterface
{
    public function getAggregateRootId(): AggregateRootIdInterface;

    /**
     * @return EventInterface[]
     */
    public function getEvents(): array;

    /**
     * @throws EventNotSupportedException
     */
    public function apply(EventInterface $event): void;

    public function remove(EventInterface $event): void;
}
