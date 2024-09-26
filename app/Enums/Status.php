<?php

namespace App\Enums;

enum Status: string
{
    case PENDING = 'pending';
    case PENDING_PAYMENT = 'pending_payment';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case REVERSED = 'reversed';
    case DISPUTED = 'disputed';
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
}
