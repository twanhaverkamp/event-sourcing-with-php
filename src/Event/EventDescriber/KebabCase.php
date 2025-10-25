<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Event\EventDescriber;

use ReflectionClass;
use ReflectionException;
use TwanHaverkamp\EventSourcingWithPhp\Event\EventInterface;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception;

class KebabCase implements EventDescriberInterface
{
    /**
     * @param EventInterface|class-string<EventInterface> $event
     *
     * @throws Exception\EventCannotBeDescribedException when a non-existing class-string was passed.
     */
    public function describe(EventInterface|string $event): string
    {
        try {
            $className = (new ReflectionClass($event))
                ->getShortName();
        } catch (ReflectionException) {
            throw new Exception\EventCannotBeDescribedException(sprintf(
                'The passed Event \'%s\' could not be described.',
                is_string($event) ? $event : $event::class,
            ));
        }

        return strtolower(
            preg_replace('/[A-Z]/', '-$0', lcfirst($className)) ?: '',
        );
    }
}
