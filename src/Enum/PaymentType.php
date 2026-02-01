<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum PaymentType: string implements TranslatableInterface
{
    case CASH = 'cash';
    case CARD = 'card';
    case NONE = 'none';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->name, [], 'enum');
    }
}
