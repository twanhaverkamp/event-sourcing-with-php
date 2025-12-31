<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Event\EventStore\Traits;

use TwanHaverkamp\EventSourcingWithPhp\Event;

trait Register
{
    /**
     * @var class-string<Event\EventInterface>[]
     */
    protected static array $registeredEventClasses = [];

    /**
     * @param class-string<Event\EventInterface> ...$eventClasses
     */
    public static function register(string ...$eventClasses): void
    {
        static::$registeredEventClasses = array_filter(
            $eventClasses,
            /** @phpstan-ignore function.alreadyNarrowedType */
            static fn (string $eventClass) => is_subclass_of($eventClass, Event\EventInterface::class),
        );
    }
}
