<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Event\EventDescriber;

use ReflectionClass;
use TwanHaverkamp\EventSourcingWithPhp\Event\EventInterface;

class KebabCase implements EventDescriberInterface
{
    public function describe(EventInterface $event): string
    {
        $className = (new ReflectionClass($event))
            ->getShortName();

        return strtolower(
            preg_replace('/[A-Z]/', '-$0', lcfirst($className)) ?: '',
        );
    }
}
