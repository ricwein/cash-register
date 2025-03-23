<?php

namespace App\Model;

final readonly class ReceiptArticle
{
    public function __construct(
        public string $name,
        public int $id,
        public int $quantity,
        public string $price,
    ) {}
}
