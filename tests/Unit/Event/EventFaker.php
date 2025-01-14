<?php

namespace TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Event;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event;
use TwanHaverkamp\EventSourcingWithPhp\Example;

class EventFaker
{
    /**
     * @return Event\EventInterface[]
     */
    public static function getEvents(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        DateTimeInterface $recordedAt = new DateTimeImmutable(),
    ): array {
        return [
            static::newInvoiceWasCreated($aggregateRootId, $recordedAt),
            static::newPaymentTransactionWasStarted($aggregateRootId, $recordedAt),
            static::newPaymentTransactionWasCompleted($aggregateRootId, $recordedAt),
            static::newPaymentTransactionWasCancelled($aggregateRootId, $recordedAt),
        ];
    }

    public static function newInvoiceWasCreated(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        DateTimeInterface $recordedAt = new DateTimeImmutable(),
    ): Example\Event\InvoiceWasCreated {
        return new Example\Event\InvoiceWasCreated(
            $aggregateRootId,
            date('Ymd') . '-' . random_int(1, 99),
            [
                new Example\Aggregate\DTO\Item(bin2hex(random_bytes(8)), 'Rubber Duck', 1, 9.95, 21.),
                new Example\Aggregate\DTO\Item(null, 'Shipping costs', 1, 2.95, 0.),
            ],
            $recordedAt,
        );
    }

    public static function newPaymentTransactionWasStarted(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        DateTimeInterface $recordedAt = new DateTimeImmutable(),
    ): Example\Event\PaymentTransactionWasStarted {
        return new Example\Event\PaymentTransactionWasStarted(
            $aggregateRootId,
            RamseyUuid::uuid7($recordedAt),
            'Bank transfer',
            14.99,
            $recordedAt,
        );
    }

    public static function newPaymentTransactionWasCompleted(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        DateTimeInterface $recordedAt = new DateTimeImmutable(),
    ): Example\Event\PaymentTransactionWasCompleted {
        return new Example\Event\PaymentTransactionWasCompleted(
            $aggregateRootId,
            RamseyUuid::uuid7($recordedAt),
            $recordedAt,
        );
    }

    public static function newPaymentTransactionWasCancelled(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        DateTimeInterface $recordedAt = new DateTimeImmutable(),
    ): Example\Event\PaymentTransactionWasCancelled {
        return new Example\Event\PaymentTransactionWasCancelled(
            $aggregateRootId,
            RamseyUuid::uuid7($recordedAt),
            $recordedAt,
        );
    }
}
