<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ApiTokens extends Page implements HasForms, HasInfolists, HasTable
{
    use InteractsWithForms, InteractsWithInfolists, InteractsWithTable;

    protected static ?string $navigationIcon = 'tabler-api';

    protected static string $view = 'filament.pages.api-tokens';

    protected static ?string $title = 'API Tokens';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    public ?string $token = '';

    public static function canAccess(): bool
    {
        return Auth::user()->is_admin;
    }

    public function tokenInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state([
                'token' => $this->token,
            ])
            ->schema([
                Section::make()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('token')
                            ->label('API Token')
                            ->formatStateUsing(fn (string $state) => explode('|', $state)[1])
                            ->helperText('Copy and save the token above, this token will not be shown again!')
                            ->color('gray')
                            ->copyable()
                            ->copyableState(fn (string $state) => explode('|', $state)[1])
                            ->fontFamily(FontFamily::Mono),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (): MorphMany => Auth::user()->tokens())
            ->headerActions([
                Action::make('createToken')
                    ->form([
                        TextInput::make('token_name')
                            ->label('Name')
                            ->maxLength('100')
                            ->required(),
                        CheckboxList::make('token_abilities')
                            ->label('Abilities')
                            ->options([
                                'result:read' => 'Read results',
                            ])
                            ->descriptions([
                                'result:read' => new HtmlString('Allow the token to retrieve result data.'),
                            ])
                            ->required()
                            ->bulkToggleable(),
                        DateTimePicker::make('token_expires_at')
                            ->label('Expires at')
                            ->nullable(),
                    ])
                    ->action(function (array $data) {
                        $token = Auth::user()->createToken(
                            name: $data['token_name'],
                            abilities: $data['token_abilities'],
                            expiresAt: $data['token_expires_at'] ? Carbon::parse($data['token_expires_at']) : null,
                        );

                        $this->token = $token->plainTextToken;
                    })
                    ->label('Create API Token')
                    ->modal('createToken')
                    ->modalWidth(MaxWidth::ExtraLarge),
            ])
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('last_used_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
