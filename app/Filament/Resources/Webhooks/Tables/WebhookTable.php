<?php

namespace App\Filament\Resources\Webhooks\Tables;

use App\Actions\Notifications\SendWebhookTestNotification;
use App\Enums\WebhookEvent;
use App\Models\Webhook;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class WebhookTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label(__('webhooks.url'))
                    ->searchable()
                    ->limit(50),
                TextColumn::make('events')
                    ->label(__('webhooks.events'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): ?string => WebhookEvent::tryFrom($state)?->getLabel()),
                IconColumn::make('enabled')
                    ->label(__('general.enable'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('general.created_at'))
                    ->alignEnd()
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                TernaryFilter::make('enabled')
                    ->label(__('general.enable'))
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('test')
                        ->label(__('webhooks.send_test'))
                        ->icon(Heroicon::PaperAirplane)
                        ->action(fn (Webhook $record) => SendWebhookTestNotification::run($record)),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
