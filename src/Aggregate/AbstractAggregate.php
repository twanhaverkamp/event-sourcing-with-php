<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Aggregate;

use TwanHaverkamp\EventSourcingWithPhp\Event\EventInterface;

abstract class AbstractAggregate implements AggregateInterface
{
    /**
     * @throws Exception\AggregateRootIdConstructionFailedException
     */
    abstract public static function init(string $aggregateRootId): self;

    /**
     * @var EventInterface[]
     */
    protected array $events = [];

    protected function __construct(
        protected readonly AggregateRootId\AggregateRootIdInterface $aggregateRootId,
    ) {
    }

    public function getAggregateRootId(): AggregateRootId\AggregateRootIdInterface
    {
        return $this->aggregateRootId;
    }

    /**
     * @return EventInterface[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function remove(EventInterface $event): void
    {
        if (($key = array_search($event, $this->events, true)) !== false) {
            unset($this->events[$key]);
        }
    }

    protected function recordThat(EventInterface $event): void
    {
        $this->events[] = $event;
        $this->apply($event);
    }
}
