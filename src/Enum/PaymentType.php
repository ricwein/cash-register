<?php

namespace App\Enum;

enum PaymentType: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case NONE = 'none';
}
