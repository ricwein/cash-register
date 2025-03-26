<?php

namespace App\DTO;

use JsonSerializable;

final readonly class Product implements JsonSerializable
{
    public function __construct(
        private int $id,
        private string $name,
        private string $price,
        private string $color,
        private ?string $icon,
        private ?string $imageUrl,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float)$this->price,
            'color' => $this->color,
            'icon' => $this->icon,
            'imageUrl' => $this->imageUrl,
        ];
    }
}
