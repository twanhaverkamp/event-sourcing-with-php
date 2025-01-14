<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Example\Event;

use DateTimeInterface;
use Ramsey\Uuid as RamseyUuid;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception;

readonly class PaymentTransactionWasStarted extends Event\AbstractEvent
{
    public function __construct(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        public RamseyUuid\UuidInterface $id,
        public string $paymentMethod,
        public float $amount,
        public DateTimeInterface $startedAt,
    ) {
        parent::__construct($aggregateRootId, $startedAt);
    }

    /**
     * @param array{
     *     id: string,
     *     paymentMethod: string,
     *     amount: float,
     * } $payload
     */
    public static function fromPayload(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        array $payload,
        DateTimeInterface $recordedAt,
    ): self {
        if (RamseyUuid\Uuid::isValid((string)$payload['id']) === false) {
            throw new Exception\EventConstructionFailedException(
                message: sprintf(
                    'Failed to create an \'id\' value from "%s" for event "%s".',
                    $payload['id'],
                    static::class,
                ),
            );
        }

        return new self(
            $aggregateRootId,
            RamseyUuid\Uuid::fromString((string)$payload['id']),
            (string)$payload['paymentMethod'],
            (float)$payload['amount'],
            $recordedAt
        );
    }

    public function getPayload(): array
    {
        return [
            'id'            => $this->id->toString(),
            'paymentMethod' => $this->paymentMethod,
            'amount'        => $this->amount,
        ];
    }
}
