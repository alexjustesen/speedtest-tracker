<?php

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Filament\Resources\ApiTokenResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createToken')
                ->label(__('translations.create_api_token'))
                ->form(ApiTokenResource::getTokenFormSchema())
                ->action(function (array $data): void {
                    $token = auth()->user()->createToken(
                        $data['name'],
                        $data['abilities'],
                        $data['expires_at'] ? Carbon::parse($data['expires_at']) : null
                    );

                    Notification::make()
                        ->title(__('translations.token_created'))
                        ->body(__('translations.your_token').': `'.explode('|', $token->plainTextToken)[1].'`')
                        ->success()
                        ->persistent()
                        ->send();
                })
                ->modalWidth('xl'),
        ];
    }
}
