<?php

namespace App\Filament\Resources\UserResource\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Section::make('Details')
                    ->columns([
                        'default' => 1,
                        'lg' => 2,
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->confirmed()
                            ->password()
                            ->revealable()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state)),

                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable(),
                    ]),
            ]),

            Grid::make(1)
                ->columnSpan(1)
                ->schema([
                    Section::make('Platform')
                        ->schema([
                            Select::make('role')
                                ->label('Role')
                                ->default(UserRole::User)
                                ->options(UserRole::class)
                                ->required()
                                ->disabled(fn (?User $record): bool => Auth::user()->role !== UserRole::Admin),
                        ]),

                    Section::make()
                        ->schema([
                            Placeholder::make('created_at')
                                ->content(fn (?User $record): string => $record ? $record->created_at->diffForHumans() : '-'),

                            Placeholder::make('updated_at')
                                ->content(fn (?User $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                        ]),
                ]),
        ];
    }
}