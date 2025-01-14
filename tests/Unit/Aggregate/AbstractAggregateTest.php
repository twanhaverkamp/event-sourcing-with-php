<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Aggregate;

use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate;
use TwanHaverkamp\EventSourcingWithPhp\Event;
use TwanHaverkamp\EventSourcingWithPhp\Example;

#[Attributes\CoversClass(Aggregate\AbstractAggregate::class)]
#[Attributes\UsesClass(Aggregate\AggregateRootId\Uuid7::class)]
#[Attributes\UsesClass(Event\AbstractEvent::class)]
class AbstractAggregateTest extends TestCase
{
    protected Aggregate\AggregateInterface $aggregate;

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'getAggregateRootId\' returns the constructed AggregateRootId')]
    public function getAggregateRootIdReturnsConstructedValue(): void
    {
        $invoice = Example\Aggregate\Invoice::init(
            ($aggregateRootId = new Aggregate\AggregateRootId\Uuid7())->toString(),
        );

        static::assertEquals($aggregateRootId, $invoice->getAggregateRootId());
    }

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'getEvents\' returns the recorded Events')]
    public function getEventsReturnsRecordedEvents(): void
    {
        $invoice = clone $this->aggregate;

        static::assertCount(3, $events = $invoice->getEvents());
        static::assertInstanceOf(Example\Event\InvoiceWasCreated::class, current($events));

        next($events);
        static::assertInstanceOf(Example\Event\PaymentTransactionWasStarted::class, current($events));

        next($events);
        static::assertInstanceOf(Example\Event\PaymentTransactionWasCompleted::class, current($events));
    }

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'remove\' deletes the recorded Event')]
    public function removeDeletesEventFromRecordedEvents(): void
    {
        $invoice = clone $this->aggregate;

        foreach ($invoice->getEvents() as $event) {
            $invoice->remove($event);

            static::assertFalse(in_array($event, $invoice->getEvents(), true));
        }

        static::assertCount(0, $invoice->getEvents());
    }

    protected function setUp(): void
    {
        $invoice = Example\Aggregate\Invoice::create(
            date('Ymd') . '-' . random_int(1, 99),
            new Example\Aggregate\DTO\Item(bin2hex(random_bytes(8)), 'Rubber Duck', 1, 9.95, 21.),
            new Example\Aggregate\DTO\Item(null, 'Shipping costs', 1, 2.95, 0.),
        );

        $paymentTransaction = $invoice->startPaymentTransaction('Bank transfer', 14.99);
        $invoice->completePaymentTransaction($paymentTransaction->id);

        $this->aggregate = $invoice;
    }
}
