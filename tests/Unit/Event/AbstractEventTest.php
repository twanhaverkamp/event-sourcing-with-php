<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Event;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event;

#[Attributes\CoversClass(Event\AbstractEvent::class)]
class AbstractEventTest extends TestCase
{
    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'getAggregateRootId\' returns the constructed AggregateRootId')]
    public function getAggregateRootIdReturnsConstructedValue(): void
    {
        $aggregateRootId = self::createStub(AggregateRootId\AggregateRootIdInterface::class);

        foreach (EventFaker::getEvents($aggregateRootId) as $event) {
            static::assertSame($aggregateRootId, $event->getAggregateRootId());
        }
    }

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'getRecordedAt\' returns the constructed DateTimeInterface')]
    public function getRecordedAtReturnsConstructedValue(): void
    {
        $aggregateRootId = self::createStub(AggregateRootId\AggregateRootIdInterface::class);
        $recordedAt = new DateTimeImmutable();

        foreach (EventFaker::getEvents($aggregateRootId, $recordedAt) as $event) {
            static::assertSame($recordedAt, $event->getRecordedAt());
        }
    }
}
