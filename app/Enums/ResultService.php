<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ResultService: string implements HasLabel
{
    case Faker = 'faker';
    case Librespeed = 'librespeed';
    case Ookla = 'ookla';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Faker => __('enums.service.faker'),
            self::Librespeed => __('enums.service.librespeed'),
            self::Ookla => __('enums.service.ookla'),
        };
    }
}
