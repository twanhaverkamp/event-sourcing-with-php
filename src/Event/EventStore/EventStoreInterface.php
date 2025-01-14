<?php

namespace TwanHaverkamp\EventSourcingWithPhp\Event\EventStore;

use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateInterface;

interface EventStoreInterface
{
    public function load(AggregateInterface $aggregate): void;


    public function save(AggregateInterface $aggregate): void;
}
