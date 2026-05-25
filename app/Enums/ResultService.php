<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ResultService: string implements HasLabel
{
    case Faker = 'faker';
    case Ookla = 'ookla';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Faker => __('enums.service.faker'),
            self::Ookla => __('enums.service.ookla'),
        };
    }
}
