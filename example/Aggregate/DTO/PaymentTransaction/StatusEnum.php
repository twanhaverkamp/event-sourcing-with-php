<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Example\Aggregate\DTO\PaymentTransaction;

enum StatusEnum: string
{
    case Started = 'started';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
