<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasLabel
{
    case Admin = 'admin';
    case User = 'user';

    public function getColor(): ?string
    {
        return match ($this) {
            self::Admin => 'success',
            self::User => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::User => 'User',
        };
    }
}
