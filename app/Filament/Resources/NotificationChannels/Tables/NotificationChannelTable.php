<?php

namespace App\Filament\Resources\NotificationChannels\Tables;

use App\Actions\Notifications\SendTestNotification;
use App\Enums\NotificationChannelType;
use App\Enums\NotificationEvent;
use App\Models\NotificationChannel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NotificationChannelTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('notification_channels.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('notification_channels.type'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('events')
                    ->label(__('notification_channels.events'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): ?string => NotificationEvent::tryFrom($state)?->getLabel()),
                IconColumn::make('status')
                    ->label(__('notification_channels.status'))
                    ->state(fn (NotificationChannel $record): bool => ! $record->isFailing())
                    ->boolean()
                    ->tooltip(fn (NotificationChannel $record): ?string => $record->isFailing()
                        ? $record->last_error
                        : __('notification_channels.healthy')),
                TextColumn::make('last_sent_at')
                    ->label(__('notification_channels.last_sent_at'))
                    ->since()
                    ->dateTimeTooltip(config('app.datetime_format'), config('app.display_timezone'))
                    ->placeholder(__('notification_channels.never'))
                    ->sortable(),
                ToggleColumn::make('enabled')
                    ->label(__('general.enable')),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('notification_channels.type'))
                    ->options(NotificationChannelType::class),
                TernaryFilter::make('enabled')
                    ->label(__('general.enable'))
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('test')
                        ->label(__('notification_channels.send_test'))
                        ->icon(Heroicon::PaperAirplane)
                        ->action(fn (NotificationChannel $record) => SendTestNotification::run($record)),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
