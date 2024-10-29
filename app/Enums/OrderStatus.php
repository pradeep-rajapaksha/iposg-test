<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Dispatched = 'dispatched';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case Returned = 'returned';
    case Cancelled = 'cancelled';
}
