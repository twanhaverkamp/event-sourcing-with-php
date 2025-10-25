<?php

namespace TwanHaverkamp\EventSourcingWithPhp\Event\EventDescriber;

use TwanHaverkamp\EventSourcingWithPhp\Event\EventInterface;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception;

interface EventDescriberInterface
{
    /**
     * @param EventInterface|class-string<EventInterface> $event
     *
     * @throws Exception\EventCannotBeDescribedException
     */
    public function describe(EventInterface|string $event): string;
}
