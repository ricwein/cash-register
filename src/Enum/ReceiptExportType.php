<?php

namespace App\Enum;

enum ReceiptExportType: string
{
    case ACCUMULATED = 'ACCUMULATED';
    case PER_EVENT = 'PER_EVENT';
}
