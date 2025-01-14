<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Example\Aggregate\DTO;

readonly class Item
{
    public function __construct(
        public string|null $reference,
        public string $description,
        public int $quantity,
        public float $price,
        public float $tax,
    ) {
    }

    /**
     * @param array{
     *     reference: string|null,
     *     description: string,
     *     quantity: int,
     *     price: float,
     *     tax: float,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['reference'],
            (string)$data['description'],
            (int)$data['quantity'],
            (float)$data['price'],
            (float)$data['tax'],
        );
    }

    /**
     * @return array{
     *     reference: string|null,
     *     description: string,
     *     quantity: int,
     *     price: float,
     *     tax: float,
     * }
     */
    public function toArray(): array
    {
        return [
            'reference'   => $this->reference,
            'description' => $this->description,
            'quantity'    => $this->quantity,
            'price'       => $this->price,
            'tax'         => $this->tax,
        ];
    }
}
