<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Settings\GeneralSettings;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getMaxContentWidth(): string
    {
        $settings = new GeneralSettings();

        return $settings->content_width;
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function beforeSave()
    {
        if (! array_key_exists('new_password', $this->data) || ! filled($this->data['new_password'])) {
            return;
        }

        $this->record->password = Hash::make($this->data['new_password']);
    }
}
