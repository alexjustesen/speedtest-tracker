<?php

namespace App\Filament\Resources\ApiTokens\Pages;

use App\Filament\Resources\ApiTokens\ApiTokenResource;
use App\Filament\Resources\ApiTokens\Schemas\ApiTokenForm;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createToken')
                ->label('Create API Token')
                ->schema(ApiTokenForm::schema())
                ->action(function (array $data): void {
                    $token = Auth::user()->createToken(
                        $data['name'],
                        $data['abilities'],
                        $data['expires_at'] ? Carbon::parse($data['expires_at']) : null
                    );

                    Notification::make()
                        ->title('Token Created')
                        ->body('Your token: `'.explode('|', $token->plainTextToken)[1].'`')
                        ->success()
                        ->persistent()
                        ->send();
                })
                ->modalWidth('xl'),
        ];
    }
}
