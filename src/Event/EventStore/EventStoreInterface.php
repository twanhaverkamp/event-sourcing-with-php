<?php

namespace TwanHaverkamp\EventSourcingWithPhp\Event\EventStore;

use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateInterface;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception;

interface EventStoreInterface
{
    /**
     * @throws Exception\EventStorageFailedException
     */
    public function load(AggregateInterface $aggregate): void;

    /**
     * @throws Exception\EventStorageFailedException
     */
    public function save(AggregateInterface $aggregate): void;
}
