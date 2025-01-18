<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Example\Aggregate;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid as RamseyUuid;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception;
use TwanHaverkamp\EventSourcingWithPhp\Example;

class Invoice extends Aggregate\AbstractAggregate
{
    public string $number;

    /**
     * @var DTO\Item[]
     */
    public array $items;

    /**
     * @var array<string<RamseyUuid\UuidInterface>, DTO\PaymentTransaction>
     */
    public array $paymentTransactions;

    public DateTimeInterface $createdAt;

    public static function init(string $aggregateRootId): self
    {
        return new self(
            AggregateRootId\Uuid7::fromString($aggregateRootId),
        );
    }

    public static function create(string $number, DTO\Item ...$items): self
    {
        $invoice = new self($aggregateRootId = new AggregateRootId\Uuid7());
        $invoice->recordThat(new Example\Event\InvoiceWasCreated(
            $aggregateRootId,
            $number,
            $items,
            new DateTimeImmutable()
        ));

        return $invoice;
    }

    public function startPaymentTransaction(string $paymentMethod, float $amount): DTO\PaymentTransaction
    {
        $this->recordThat(new Example\Event\PaymentTransactionWasStarted(
            $this->aggregateRootId,
            $id = RamseyUuid\Uuid::uuid7($startedAt = new DateTimeImmutable()),
            $paymentMethod,
            $amount,
            $startedAt,
        ));

        return $this->paymentTransactions[$id->toString()];
    }

    public function completePaymentTransaction(RamseyUuid\UuidInterface $id): void
    {
        $this->recordThat(new Example\Event\PaymentTransactionWasCompleted(
            $this->aggregateRootId,
            $id,
            new DateTimeImmutable(),
        ));
    }

    public function cancelPaymentTransaction(RamseyUuid\UuidInterface $id): void
    {
        $this->recordThat(new Example\Event\PaymentTransactionWasCancelled(
            $this->aggregateRootId,
            $id,
            new DateTimeImmutable(),
        ));
    }

    public function getSubTotal(): float
    {
        return (float)number_format(
            array_sum(
                array_map(
                    fn (DTO\Item $item) => round($item->price * 100, 0) * $item->quantity,
                    $this->items,
                ),
            ) / 100,
            2,
            thousands_separator: null,
        );
    }

    public function getTax(): float
    {
        return (float)number_format(
            array_sum(
                array_map(
                    fn (DTO\Item $item) => round($item->price * 100, 0) * $item->quantity * ($item->tax / 100),
                    $this->items,
                ),
            ) / 100,
            2,
            thousands_separator: null,
        );
    }

    public function getTotal(): float
    {
        return $this->getSubTotal() + $this->getTax() - (float)number_format(
            array_sum(
                array_map(
                    fn (DTO\PaymentTransaction $paymentTransaction) => $paymentTransaction->amount,
                    array_filter(
                        $this->paymentTransactions,
                        fn (DTO\PaymentTransaction $paymentTransaction) => $paymentTransaction->isCompleted(),
                    ),
                ),
            )
        );
    }

    public function getPaymentDueAt(): DateTimeInterface
    {
        return DateTimeImmutable::createFromInterface($this->createdAt)
            ->modify('+1 month');
    }

    public function apply(Event\EventInterface $event): void
    {
        match ($event::class) {
            Example\Event\InvoiceWasCreated::class              => $this->applyInvoiceWasCreated($event),
            Example\Event\PaymentTransactionWasStarted::class   => $this->applyPaymentTransactionWasStarted($event),
            Example\Event\PaymentTransactionWasCompleted::class => $this->applyPaymentTransactionWasCompleted($event),
            Example\Event\PaymentTransactionWasCancelled::class => $this->applyPaymentTransactionWasCancelled($event),
            default => throw new Exception\EventNotSupportedException(
                message: sprintf(
                    'Event "%s" is not supported by "%s" aggregate.',
                    $event::class,
                    $this::class,
                ),
            ),
        };
    }

    private function applyInvoiceWasCreated(Example\Event\InvoiceWasCreated $event): void
    {
        $this->number    = $event->number;
        $this->items     = $event->items;
        $this->createdAt = clone $event->createdAt;
    }

    private function applyPaymentTransactionWasStarted(Example\Event\PaymentTransactionWasStarted $event): void
    {
        $this->paymentTransactions[$event->id->toString()] = new DTO\PaymentTransaction(
            $event->id,
            $event->paymentMethod,
            $event->amount,
            $event->startedAt,
        );
    }

    private function applyPaymentTransactionWasCompleted(Example\Event\PaymentTransactionWasCompleted $event): void
    {
        $this->paymentTransactions[$event->id->toString()]->complete($event->completedAt);
    }

    private function applyPaymentTransactionWasCancelled(Example\Event\PaymentTransactionWasCancelled $event): void
    {
        $this->paymentTransactions[$event->id->toString()]->cancel($event->cancelledAt);
    }
}
