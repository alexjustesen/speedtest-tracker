<?php

namespace App\Filament\Pages\Settings;

use App\Enums\UserRole;
use App\Settings\SsoSettings;
use App\Sso\Contracts\SsoConnectionTester;
use App\Sso\SsoManager;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class SingleSignOn extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-shield-lock';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    protected static string $settings = SsoSettings::class;

    public function getTitle(): string
    {
        return __('settings/sso.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings/sso.label');
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $manager = app(SsoManager::class);
        $provider = $manager->activeProvider();

        return [
            ...$data,
            'enabled' => $manager->enabled(),
            'provider' => $provider ?? ($data['provider'] ?? null),
            'client_id' => $manager->clientId(),
            'client_secret' => $manager->clientSecret(),
            'base_url' => $manager->baseUrl(),
            'scopes' => $provider ? $manager->scopes($provider) : ($data['scopes'] ?? []),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('enabled')
                    ->label(__('settings/sso.enabled'))
                    ->helperText(__('settings/sso.enabled_helper'))
                    ->live()
                    ->columnSpanFull(),

                Section::make(__('settings/sso.connection'))
                    ->hidden(fn (Get $get): bool => ! $get('enabled'))
                    ->columns(2)
                    ->schema([
                        Select::make('provider')
                            ->label(__('settings/sso.provider'))
                            ->options(collect(config('sso.providers'))
                                ->map(fn (array $meta, string $key): string => $meta['label'] ?? $key)
                                ->all())
                            ->required()
                            ->live()
                            ->native(false),
                        TextInput::make('base_url')
                            ->label(__('settings/sso.base_url'))
                            ->helperText(__('settings/sso.base_url_helper'))
                            ->url()
                            ->required(fn (Get $get): bool => (bool) $get('enabled')),
                        TextInput::make('client_id')
                            ->label(__('settings/sso.client_id'))
                            ->required(fn (Get $get): bool => (bool) $get('enabled')),
                        TextInput::make('client_secret')
                            ->label(__('settings/sso.client_secret'))
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => (bool) $get('enabled')),
                        TagsInput::make('scopes')
                            ->label(__('settings/sso.scopes'))
                            ->helperText(__('settings/sso.scopes_helper'))
                            ->splitKeys(['Tab', ' ', ','])
                            ->columnSpanFull(),
                        TextInput::make('button_label')
                            ->label(__('settings/sso.button_label'))
                            ->helperText(__('settings/sso.button_label_helper'))
                            ->columnSpanFull(),
                        Placeholder::make('redirect_uri')
                            ->label(__('settings/sso.redirect_uri'))
                            ->helperText(__('settings/sso.redirect_uri_helper'))
                            ->content(fn (Get $get): string => route('sso.callback', $get('provider') ?: 'authentik'))
                            ->columnSpanFull(),
                        Actions::make([
                            Action::make('test_connection')
                                ->label(__('settings/sso.test_connection'))
                                ->icon('heroicon-o-check-circle')
                                ->action(function (Get $get, SsoConnectionTester $tester): void {
                                    $result = $tester->test(
                                        (string) $get('provider'),
                                        $get('base_url'),
                                        $get('client_id'),
                                        $get('client_secret'),
                                    );

                                    Notification::make()
                                        ->title($result->message)
                                        ->status($result->ok ? 'success' : 'danger')
                                        ->send();
                                }),
                        ]),
                    ]),

                Section::make(__('settings/sso.provisioning'))
                    ->hidden(fn (Get $get): bool => ! $get('enabled'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('auto_create_users')
                            ->label(__('settings/sso.auto_create_users'))
                            ->helperText(__('settings/sso.auto_create_users_helper')),
                        Toggle::make('allow_linking_by_email')
                            ->label(__('settings/sso.allow_linking_by_email'))
                            ->helperText(__('settings/sso.allow_linking_by_email_helper')),
                        Select::make('default_role')
                            ->label(__('settings/sso.default_role'))
                            ->helperText(__('settings/sso.default_role_helper'))
                            ->options(UserRole::class)
                            ->required()
                            ->native(false),
                    ]),

                Section::make(__('settings/sso.role_mapping'))
                    ->hidden(fn (Get $get): bool => ! $get('enabled'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('role_mapping_enabled')
                            ->label(__('settings/sso.role_mapping_enabled'))
                            ->helperText(__('settings/sso.role_mapping_enabled_helper'))
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('groups_claim')
                            ->label(__('settings/sso.groups_claim'))
                            ->helperText(__('settings/sso.groups_claim_helper'))
                            ->hidden(fn (Get $get): bool => ! $get('role_mapping_enabled')),
                        TagsInput::make('admin_groups')
                            ->label(__('settings/sso.admin_groups'))
                            ->helperText(__('settings/sso.admin_groups_helper'))
                            ->hidden(fn (Get $get): bool => ! $get('role_mapping_enabled')),
                    ]),
            ]);
    }
}
