<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Example\Event;

use DateTimeInterface;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event;
use TwanHaverkamp\EventSourcingWithPhp\Example\Aggregate\DTO;

readonly class InvoiceWasCreated extends Event\AbstractEvent
{
    /**
     * @param DTO\Item[] $items
     */
    public function __construct(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        public string $number,
        public array $items,
        public DateTimeInterface $createdAt,
    ) {
        parent::__construct($aggregateRootId, $createdAt);
    }

    /**
     * @param array{
     *     number: string,
     *     items: array{
     *         reference: string|null,
     *         description: string,
     *         quantity: int,
     *         price: float,
     *         tax: float,
     *     }[],
     * } $payload
     */
    public static function fromPayload(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        array $payload,
        DateTimeInterface $recordedAt,
    ): self {
        return new self(
            $aggregateRootId,
            (string)$payload['number'],
            array_map(fn (array $item) => DTO\Item::fromArray($item), $payload['items']),
            $recordedAt,
        );
    }

    public function getPayload(): array
    {
        return [
            'number' => $this->number,
            'items'  => array_map(fn (DTO\Item $item) => $item->toArray(), $this->items),
        ];
    }
}
