<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Example\Aggregate\DTO;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class PaymentTransaction
{
    private PaymentTransaction\StatusEnum $status = PaymentTransaction\StatusEnum::Started;

    private ?DateTimeInterface $completedAt = null;
    private ?DateTimeInterface $cancelledAt = null;

    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $paymentMethod,
        public readonly float $amount,
        public readonly DateTimeInterface $startedAt,
    ) {
    }

    public function complete(DateTimeInterface $completedAt): void
    {
        $this->status = PaymentTransaction\StatusEnum::Completed;
        $this->completedAt = $completedAt;
    }

    public function cancel(DateTimeInterface $cancelledAt): void
    {
        $this->status = PaymentTransaction\StatusEnum::Cancelled;
        $this->cancelledAt = $cancelledAt;
    }

    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completedAt;
    }

    public function getCancelledAt(): ?DateTimeInterface
    {
        return $this->cancelledAt;
    }

    public function isCompleted(): bool
    {
        return $this->status === PaymentTransaction\StatusEnum::Completed;
    }

    public function isCancelled(): bool
    {
        return $this->status === PaymentTransaction\StatusEnum::Cancelled;
    }
}
