<?php

namespace TwanHaverkamp\EventSourcingWithPhp\Event\EventDescriber;

use TwanHaverkamp\EventSourcingWithPhp\Event\EventInterface;

interface EventDescriberInterface
{
    public function describe(EventInterface $event): string;
}
