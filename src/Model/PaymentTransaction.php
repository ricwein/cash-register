<?php

namespace App\Model;

use App\Enum\PaymentType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

readonly class PaymentTransaction
{
    public function __construct(
        #[Assert\Type(PaymentType::class)]
        public string $paymentType,

        #[Assert\All([new Assert\Type('integer'), new Assert\GreaterThanOrEqual(1)])]
        public array $cart,

        #[Assert\AtLeastOneOf([
            new Assert\Uuid(versions: 4),
            new Assert\Blank(),
            new Assert\IsNull(),
        ])]
        public ?string $uuid = null,
    ) {}

    public function getUuid(): Uuid
    {
        return $this->uuid !== null
            ? UuidV4::fromString($this->uuid)
            : Uuid::v4();
    }

    public function getPaymentType(): PaymentType
    {
        return PaymentType::from($this->paymentType);
    }
}
