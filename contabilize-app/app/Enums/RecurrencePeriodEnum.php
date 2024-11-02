<?php

namespace App\Enums;

enum RecurrencePeriodEnum: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case ANNUALLY = 'annually';

    public function label(): string
    {
        return match ($this) {
            self::DAILY => 'Diário',
            self::WEEKLY => 'Semanal',
            self::MONTHLY => 'Mensal',
            self::ANNUALLY => 'Anual',
        };
    }
}
