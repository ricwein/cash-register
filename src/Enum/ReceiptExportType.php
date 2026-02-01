<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ReceiptExportType: string implements TranslatableInterface
{
    case ACCUMULATED = 'ACCUMULATED';
    case PER_EVENT = 'PER_EVENT';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->name, [], 'enum');
    }
}
