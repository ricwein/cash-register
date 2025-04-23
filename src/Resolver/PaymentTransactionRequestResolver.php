<?php

namespace App\Resolver;

use App\Enum\PaymentType;
use App\Model\PaymentTransaction;
use InvalidArgumentException;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PaymentTransactionRequestResolver implements ValueResolverInterface
{
    /**
     * @throws JsonException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== PaymentTransaction::class) {
            return [];
        }

        /** @noinspection JsonEncodingApiUsageInspection */
        $requestData = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        if (!is_array($requestData) || !isset($requestData['paymentType'], $requestData['cart'])) {
            return [];
        }

        return [new PaymentTransaction(
            paymentType: PaymentType::from($requestData['paymentType']),
            cart: $requestData['cart'],
            uuid: $requestData['uuid'] ?? null,
        )];
    }
}
