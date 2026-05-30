<?php

namespace App\Filament\Resources\Webhooks\Schemas;

use App\Enums\WebhookEvent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class WebhookForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('url')
                ->label(__('webhooks.url'))
                ->placeholder('https://webhook.site/longstringofcharacters')
                ->url()
                ->required()
                ->maxLength(2000),

            Select::make('events')
                ->label(__('webhooks.events'))
                ->helperText(__('webhooks.events_helper'))
                ->multiple()
                ->options(WebhookEvent::groupedOptions())
                ->searchable(false)
                ->required(),

            TextInput::make('secret')
                ->label(__('webhooks.secret'))
                ->helperText(__('webhooks.secret_helper'))
                ->password()
                ->revealable()
                ->maxLength(255),

            Toggle::make('enabled')
                ->label(__('general.enable'))
                ->default(true),
        ];
    }
}
