<?php

namespace App\Model;

use App\Enum\PaymentType;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

readonly class PaymentTransaction
{
    public function __construct(
        public PaymentType $paymentType,

        #[Assert\All([new Assert\Type('integer'), new Assert\GreaterThanOrEqual(1)])]
        public array $cart,

        #[Assert\AtLeastOneOf([new Assert\Uuid(versions: 4), new Assert\IsNull()])]
        private ?string $uuid = null,
    ) {}

    public function getUuid(): Uuid
    {
        return $this->uuid !== null
            ? UuidV4::fromString($this->uuid)
            : Uuid::v4();
    }
}
