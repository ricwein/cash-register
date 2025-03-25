<?php

namespace App\Model;

use App\Enum\PaymentType;

final readonly class ReceiptArticle
{
    public function __construct(
        public string $name,
        public int $id,
        public int $quantity,
        public string $price,
        public ?PaymentType $paymentType = null,
    ) {}

    public function add(self $other): self
    {
        return new self(
            $this->name,
            $this->id,
            $this->quantity + $other->quantity,
            bcadd($this->price, $other->price, 2),
            null
        );
    }
}
