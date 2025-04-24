<?php

namespace App\Enum;

enum ConfirmationState: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case RETRYABLE_ERROR = 'retryable';
}
