<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Settings\GeneralSettings;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getMaxContentWidth(): string
    {
        $settings = new GeneralSettings();

        return $settings->content_width;
    }
}
