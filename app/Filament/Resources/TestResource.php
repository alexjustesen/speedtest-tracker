<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestResource\Pages;
use App\Filament\Resources\TestResource\RelationManagers;
use App\Models\Test;
use App\Rules\Cron;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])->schema([
                    Grid::make([
                        'default' => 1,
                        'lg' => 2,
                    ])->schema([
                        Section::make('Details')
                            ->schema([
                                TextInput::make('name')
                                    ->placeholder('Enter a name for the test.')
                                    ->maxLength(255),

                                MarkdownEditor::make('description')
                                    ->placeholder('Markdown is supported.')
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'strike',
                                    ]),

                                // ...
                            ]),

                        Tabs::make('Options')
                            ->tabs([
                                Tab::make('Schedule')
                                    ->schema([
                                        TextInput::make('options.cron_expression')
                                            ->placeholder('Enter a cron expression.')
                                            ->helperText(new HtmlString('This is a cron expression that determines when the test should run.'))
                                            ->required()
                                            ->rules([new Cron()])
                                            ->live(),

                                        // add placeholder after a scheduled is created for the next run at

                                        // ...
                                    ]),

                                Tab::make('Servers')
                                    ->schema([
                                        Radio::make('options.server_preference')
                                            ->options([
                                                'auto' => 'Automatically select a server',
                                                'prefer' => 'Prefer servers from the list',
                                                'ignore' => 'Ignore servers from the list',
                                            ])
                                            ->default('auto')
                                            ->required()
                                            ->live(),

                                        Repeater::make('options.servers')
                                            ->schema([
                                                TextInput::make('server_id')
                                                    ->label('Server ID')
                                                    ->placeholder('Enter the ID of the server.')
                                                    ->integer()
                                                    ->required(),

                                                // ...
                                            ])
                                            ->minItems(1)
                                            ->maxItems(20)
                                            ->hidden(fn (Get $get) => $get('options.server_preference') === 'auto'),

                                        // ...
                                    ]),

                                Tab::make('Advanced')
                                    ->schema([
                                        TagsInput::make('options.skip_ips')
                                            ->label('Skip IP addresses')
                                            ->placeholder('Add external Ip addresses that should be skipped.'),

                                        // ...
                                    ]),

                                // ...
                            ])
                            ->columnSpanFull(),

                        // ...
                    ])->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                    Grid::make([
                        'default' => 1,
                    ])->schema([
                        Section::make('Settings')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->required(),

                                Select::make('owned_by_id')
                                    ->label('Owner')
                                    ->placeholder('Select an owner.')
                                    ->relationship('ownedBy', 'name')
                                    ->default(Auth::id())
                                    ->searchable(),

                                TextInput::make('token')
                                    ->helperText(new HtmlString('This is a secret token that can be used to authenticate requests to the test.'))
                                    ->readOnly()
                                    ->hiddenOn('create'),

                                // ...
                            ]),

                        // ...
                    ])->columnSpan([
                        'default' => 1,
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('token')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->alignCenter()
                    ->boolean(),
                TextColumn::make('ownedBy.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('next_run_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTests::route('/'),
            'create' => Pages\CreateTest::route('/create'),
            'edit' => Pages\EditTest::route('/{record}/edit'),
        ];
    }
}
