<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\Exception\AggregateRootIdConstructionFailedException;

class Uuid7 implements AggregateRootIdInterface
{
    protected UuidInterface $uuid;

    public function __construct()
    {
        $this->uuid = RamseyUuid::uuid7();
    }

    public static function fromString(string $aggregateRootId): self
    {
        if (RamseyUuid::isValid($aggregateRootId) === false) {
            throw new AggregateRootIdConstructionFailedException(
                message: sprintf(
                    'Failed to construct aggregateRootId "%s" from string: "%s".',
                    static::class,
                    $aggregateRootId,
                ),
            );
        }

        $uuid = new self();
        $uuid->uuid = RamseyUuid::fromString($aggregateRootId);

        return $uuid;
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }
}
