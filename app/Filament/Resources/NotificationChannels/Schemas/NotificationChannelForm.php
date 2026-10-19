<?php

namespace App\Filament\Resources\NotificationChannels\Schemas;

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEvent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Validation\Rule;

class NotificationChannelForm
{
    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function schema(): array
    {
        return [
            Toggle::make('enabled')
                ->label(__('general.enable'))
                ->default(true),

            TextInput::make('name')
                ->label(__('notification_channels.name'))
                ->placeholder(__('notification_channels.name_placeholder'))
                ->required()
                ->maxLength(255),

            Select::make('type')
                ->label(__('notification_channels.type'))
                ->options(NotificationChannelType::class)
                ->native(false)
                ->required()
                ->live()
                ->disabledOn('edit')
                ->afterStateUpdated(function (Set $set, mixed $state): void {
                    $type = self::resolveType($state);

                    $set('config', []);
                    $set('events', []);
                    $set('include_unscheduled', $type?->includesUnscheduledByDefault() ?? false);
                }),

            // The config fields depend on the selected type, so they're built
            // from the type's driver whenever the type changes.
            Group::make(fn (Get $get): array => ($type = self::resolveType($get('type'))) === null ? [] : [
                Fieldset::make(__('notification_channels.configuration'))
                    ->columns(1)
                    ->statePath('config')
                    ->schema($type->driver()->configSchema()),
            ])->key('config_group'),

            Fieldset::make(__('notification_channels.triggers'))
                ->columns(1)
                ->visible(fn (Get $get): bool => self::resolveType($get('type')) !== null)
                ->schema([
                    Select::make('events')
                        ->label(__('notification_channels.events'))
                        ->helperText(__('notification_channels.events_helper'))
                        ->multiple()
                        ->options(NotificationEvent::groupedOptions())
                        ->searchable(false)
                        ->required()
                        ->nestedRecursiveRules([Rule::in(array_column(NotificationEvent::cases(), 'value'))]),

                    Toggle::make('include_unscheduled')
                        ->label(__('notification_channels.include_unscheduled'))
                        ->helperText(__('notification_channels.include_unscheduled_helper')),
                ]),
        ];
    }

    /**
     * Resolve the `type` field state, which is the enum instance when hydrated
     * from the record but a plain string once the user changes the select.
     */
    private static function resolveType(mixed $state): ?NotificationChannelType
    {
        return $state instanceof NotificationChannelType
            ? $state
            : NotificationChannelType::tryFrom((string) $state);
    }
}
