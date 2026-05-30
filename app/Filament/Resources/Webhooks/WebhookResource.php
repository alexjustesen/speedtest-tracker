<?php

namespace App\Filament\Resources\Webhooks;

use App\Filament\Resources\Webhooks\Pages\ListWebhooks;
use App\Filament\Resources\Webhooks\Schemas\WebhookForm;
use App\Filament\Resources\Webhooks\Tables\WebhookTable;
use App\Models\Webhook;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-webhook';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    public static function getLabel(): ?string
    {
        return __('webhooks.webhook');
    }

    public static function getPluralLabel(): ?string
    {
        return __('webhooks.webhooks');
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
        return $schema->components(WebhookForm::schema())->columns(1);
    }

    public static function table(Table $table): Table
    {
        return WebhookTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebhooks::route('/'),
        ];
    }
}
