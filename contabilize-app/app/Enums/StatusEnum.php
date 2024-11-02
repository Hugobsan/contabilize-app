<?php

namespace App\Enums;

enum StatusEnum: int {
    case PENDING = 0;
    case PAID = 1;

    public function label(): string {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::PAID => 'Paga',
        };
    }
}
