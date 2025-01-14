<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Aggregate\AggregateRootId;

use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\Exception;

#[Attributes\CoversClass(AggregateRootId\Uuid7::class)]
#[Attributes\UsesClass(Exception\AggregateRootIdConstructionFailedException::class)]
class Uuid7Test extends TestCase
{
    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'fromString\' throws an AggregateRootIdConstructionFailedException')]
    public function fromStringWithInvalidArgumentThrowsAggregateRootIdConstructionFailedException(): void
    {
        $this->expectException(Exception\AggregateRootIdConstructionFailedException::class);
        $this->expectExceptionMessage(sprintf(
            'Failed to construct aggregateRootId "%s" from string: "%s".',
            AggregateRootId\Uuid7::class,
            $aggregateRootId = 'invalid-aggregate-root-id-string',
        ));

        AggregateRootId\Uuid7::fromString($aggregateRootId);
    }

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'fromString\' with a valid UUID v7 string returns a new instance')]
    public function fromStringWithValidArgumentReturnsAggregateRootId(): void
    {
        $aggregateRootId = AggregateRootId\Uuid7::fromString('01941d8f-9951-72af-b5ce-5aa7aa23ea68');

        static::assertInstanceOf(AggregateRootId\Uuid7::class, $aggregateRootId);
    }

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'toString\' returns a valid UUID v7 string')]
    public function toStringReturnsUuidString(): void
    {
        $aggregateRootId = (new AggregateRootId\Uuid7())->toString();

        static::assertTrue(RamseyUuid::isValid($aggregateRootId));
    }
}
