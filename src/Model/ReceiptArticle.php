<?php

namespace App\Model;

use App\Enum\PaymentType;
use BcMath\Number;

final readonly class ReceiptArticle
{
    public Number $price;

    public function __construct(
        public string $name,
        public int $id,
        public int $quantity,
        string|int|Number $price,
        public ?PaymentType $paymentType = null,
    ) {
        $this->price = $price instanceof Number ? $price : new Number($price);
    }

    public function add(self $other): self
    {
        return new self(
            $this->name,
            $this->id,
            $this->quantity + $other->quantity,
            $this->price->add($other->price, 2),
            null,
        );
    }
}
