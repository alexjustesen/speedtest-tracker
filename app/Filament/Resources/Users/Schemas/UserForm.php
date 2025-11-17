<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function schema(): array
    {
        return [
            Grid::make([
                'default' => 1,
            ])->columnSpan([
                'lg' => 2,
            ])->schema([
                Section::make(__('general.details'))
                    ->columns([
                        'default' => 1,
                        'lg' => 2,
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label(__('general.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label(__('general.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->label(__('general.password'))
                            ->confirmed()
                            ->password()
                            ->revealable()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state)),

                        TextInput::make('password_confirmation')
                            ->label(__('general.password_confirmation'))
                            ->password()
                            ->revealable(),
                    ]),
            ]),

            Grid::make(1)
                ->columnSpan(1)
                ->schema([
                    Section::make(__('general.platform'))
                        ->schema([
                            Select::make('role')
                                ->label(__('general.role'))
                                ->default(UserRole::User)
                                ->options(UserRole::class)
                                ->required()
                                ->disabled(fn (?User $record): bool => Auth::user()->role !== UserRole::Admin),
                        ]),

                    Section::make()
                        ->schema([
                            Placeholder::make('created_at')
                                ->label(__('general.created_at'))
                                ->content(fn (?User $record): string => $record ? $record->created_at->diffForHumans() : '-'),

                            Placeholder::make('updated_at')
                                ->label(__('general.updated_at'))
                                ->content(fn (?User $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                        ]),
                ]),
        ];
    }
}
