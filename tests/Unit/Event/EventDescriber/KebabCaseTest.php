<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Event\EventDescriber;

use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event\EventDescriber;
use TwanHaverkamp\EventSourcingWithPhp\Event;
use TwanHaverkamp\EventSourcingWithPhp\Example;
use TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Event\EventFaker;

#[Attributes\CoversClass(EventDescriber\KebabCase::class)]
class KebabCaseTest extends TestCase
{
    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'describe\' returns a kebab-case string')]
    #[Attributes\DataProvider('getEventsWithExpectedString')]
    public function describeReturnsKebabCase(Event\EventInterface $event, string $expected): void
    {
        $actual = (new EventDescriber\KebabCase())
            ->describe($event);

        static::assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{
     *     event: Event\EventInterface,
     *     expected: string
     * }>
     */
    public static function getEventsWithExpectedString(): array
    {
        $aggregateRootId = self::createStub(AggregateRootId\AggregateRootIdInterface::class);

        return [
            'InvoiceWasCreated event, expects \'invoice-was-created\'' => [
                'event'    => EventFaker::newInvoiceWasCreated($aggregateRootId),
                'expected' => 'invoice-was-created',
            ],
            'PaymentTransactionWasStarted event, expects \'payment-transaction-was-started\'' => [
                'event'    => EventFaker::newPaymentTransactionWasStarted($aggregateRootId),
                'expected' => 'payment-transaction-was-started',
            ],
            'PaymentTransactionWasCompleted event, expects \'payment-transaction-was-completed\'' => [
                'event'    => EventFaker::newPaymentTransactionWasCompleted($aggregateRootId),
                'expected' => 'payment-transaction-was-completed',
            ],
            'PaymentTransactionWasCancelled event, expects \'payment-transaction-was-cancelled\'' => [
                'event'    => EventFaker::newPaymentTransactionWasCancelled($aggregateRootId),
                'expected' => 'payment-transaction-was-cancelled',
            ],
        ];
    }
}
