<?php

namespace App\DTO;

use JsonSerializable;

final readonly class Category implements JsonSerializable
{
    /** @param array<Product> $products */
    public function __construct(
        private int $id,
        private string $name,
        private string $color,
        private array $products,
    ) {}

    public function withProducts(array $products): self
    {
        return new self(
            id: $this->id,
            name: $this->name,
            color: $this->color,
            products: [...$this->products, ...$products],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'products' => $this->products,
        ];
    }
}
