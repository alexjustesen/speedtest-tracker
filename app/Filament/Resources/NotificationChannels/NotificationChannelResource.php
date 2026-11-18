<?php

namespace App\Filament\Resources\NotificationChannels;

use App\Filament\Resources\NotificationChannels\Pages\ListNotificationChannels;
use App\Filament\Resources\NotificationChannels\Schemas\NotificationChannelForm;
use App\Filament\Resources\NotificationChannels\Tables\NotificationChannelTable;
use App\Models\NotificationChannel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class NotificationChannelResource extends Resource
{
    protected static ?string $model = NotificationChannel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-bell-ringing';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    public static function getLabel(): ?string
    {
        return __('notification_channels.channel');
    }

    public static function getPluralLabel(): ?string
    {
        return __('notification_channels.channels');
    }

    public static function getNavigationLabel(): string
    {
        return __('notification_channels.label');
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(NotificationChannelForm::schema())->columns(1);
    }

    public static function table(Table $table): Table
    {
        return NotificationChannelTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationChannels::route('/'),
        ];
    }
}
