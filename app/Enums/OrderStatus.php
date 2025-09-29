<?php

namespace App\Enums;

enum OrderStatus: int
{
    case COMPLETE = 1;

    public function label(): string
    {
        return match ($this) {
            self::COMPLETE => __('Complete'),
        };
    }
}
