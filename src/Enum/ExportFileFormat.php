<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ExportFileFormat: string implements TranslatableInterface
{
    case CSV = 'csv';
    case EXCEL = 'xlsx';
    case PDF = 'pdf';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->name;
    }
}
