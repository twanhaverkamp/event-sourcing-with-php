<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Event\EventDescriber;

use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event\EventDescriber;
use TwanHaverkamp\EventSourcingWithPhp\Event;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception\EventCannotBeDescribedException;
use TwanHaverkamp\EventSourcingWithPhp\Example;
use TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Event\EventFaker;

#[Attributes\CoversClass(EventDescriber\KebabCase::class)]
class KebabCaseTest extends TestCase
{
    /**
     * @param Event\EventInterface|class-string<Event\EventInterface> $event
     */
    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'describe\' returns a kebab-case string')]
    #[Attributes\DataProvider('getEventsWithExpectedString')]
    public function describeReturnsKebabCase(Event\EventInterface|string $event, string $expected): void
    {
        $actual = (new EventDescriber\KebabCase())
            ->describe($event);

        static::assertSame($expected, $actual);
    }

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'describe\' throws an EventCannotBeDescribedException')]
    public function describeWithInvalidClassStringThrowsEventCannotBeDescribedException(): void
    {
        $this->expectException(EventCannotBeDescribedException::class);
        $this->expectExceptionMessage('The passed Event \'invalid-class-string\' could not be described.');

        (new EventDescriber\KebabCase())
            /** @phpstan-ignore argument.type */
            ->describe('invalid-class-string');
    }

    /**
     * @return array<string, array{
     *     event: Event\EventInterface|class-string<Event\EventInterface>,
     *     expected: string
     * }>
     */
    public static function getEventsWithExpectedString(): array
    {
        $aggregateRootId = static::createStub(AggregateRootId\AggregateRootIdInterface::class);

        return [
            'InvoiceWasCreated class, expects \'invoice-was-created\'' => [
                'event'    => EventFaker::newInvoiceWasCreated($aggregateRootId),
                'expected' => 'invoice-was-created',
            ],
            'InvoiceWasCreated class-string, expects \'invoice-was-created\'' => [
                'event'    => Example\Event\InvoiceWasCreated::class,
                'expected' => 'invoice-was-created',
            ],
            'PaymentTransactionWasStarted class, expects \'payment-transaction-was-started\'' => [
                'event'    => EventFaker::newPaymentTransactionWasStarted($aggregateRootId),
                'expected' => 'payment-transaction-was-started',
            ],
            'PaymentTransactionWasStarted class-string, expects \'payment-transaction-was-started\'' => [
                'event'    => Example\Event\PaymentTransactionWasStarted::class,
                'expected' => 'payment-transaction-was-started',
            ],
            'PaymentTransactionWasCompleted class, expects \'payment-transaction-was-completed\'' => [
                'event'    => EventFaker::newPaymentTransactionWasCompleted($aggregateRootId),
                'expected' => 'payment-transaction-was-completed',
            ],
            'PaymentTransactionWasCompleted class-string, expects \'payment-transaction-was-completed\'' => [
                'event'    => Example\Event\PaymentTransactionWasCompleted::class,
                'expected' => 'payment-transaction-was-completed',
            ],
            'PaymentTransactionWasCancelled class, expects \'payment-transaction-was-cancelled\'' => [
                'event'    => EventFaker::newPaymentTransactionWasCancelled($aggregateRootId),
                'expected' => 'payment-transaction-was-cancelled',
            ],
            'PaymentTransactionWasCancelled class-string, expects \'payment-transaction-was-cancelled\'' => [
                'event'    => Example\Event\PaymentTransactionWasCancelled::class,
                'expected' => 'payment-transaction-was-cancelled',
            ],
        ];
    }
}
