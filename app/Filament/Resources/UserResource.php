<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string
    {
        return __('translations.settings');
    }

    public function getTitle(): string
    {
        return __('translations.users');
    }

    public static function getNavigationLabel(): string
    {
        return __('translations.users');
    }

    public static function getLabel(): string
    {
        return __('translations.users');
    }

    public static function getPluralLabel(): string
    {
        return __('translations.users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                ])->columnSpan([
                    'lg' => 2,
                ])->schema([
                    Section::make(__('translations.details'))
                        ->columns([
                            'default' => 1,
                            'lg' => 2,
                        ])
                        ->schema([
                            TextInput::make('name')
                                ->label(__('translations.name'))
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('email')
                                ->label(__('translations.email'))
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('password')
                                ->label(__('translations.password'))
                                ->confirmed()
                                ->password()
                                ->revealable()
                                ->required(fn (string $context): bool => $context === 'create')
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state)),

                            TextInput::make('password_confirmation')
                                ->label(__('translations.password_confirmation'))
                                ->password()
                                ->revealable(),

                            // ...
                        ]),
                ]),

                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Section::make(__('translations.platform'))
                            ->schema([
                                Select::make('role')
                                    ->label(__('translations.role'))
                                    ->default(UserRole::User)
                                    ->options(UserRole::class)
                                    ->required()
                                    ->disabled(fn (?User $record): bool => Auth::user()->role !== UserRole::Admin),

                                // ...
                            ]),

                        Section::make()
                            ->schema([
                                Placeholder::make('created_at')
                                    ->label(__('translations.created_at'))
                                    ->content(fn (?User $record): string => $record ? $record->created_at->diffForHumans() : '-'),

                                Placeholder::make('updated_at')
                                    ->label(__('translations.updated_at'))
                                    ->content(fn (?User $record): string => $record ? $record->updated_at->diffForHumans() : '-'),

                                // ...
                            ]),
                    ]),
            ])
            ->columns([
                'default' => 1,
                'lg' => 3,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('translations.name'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('translations.email'))
                    ->searchable(),

                TextColumn::make('role')
                    ->label(__('translations.role'))
                    ->badge(),

                TextColumn::make('created_at')
                    ->label(__('translations.created_at'))
                    ->alignEnd()
                    ->dateTime(app(GeneralSettings::class)->datetime_format)
                    ->timezone(app(GeneralSettings::class)->display_timezone)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->label(__('translations.updated_at'))
                    ->alignEnd()
                    ->dateTime(app(GeneralSettings::class)->datetime_format)
                    ->timezone(app(GeneralSettings::class)->display_timezone)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ...
            ])
            ->filters([
                SelectFilter::make(__('translations.role'))
                    ->options(UserRole::class),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
