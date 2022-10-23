<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-table';

    protected static ?string $navigationLabel = 'Results';

    public static function table(Table $table): Table
    {
        $settings = new GeneralSettings();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                ViewColumn::make('download')
                    ->view('tables.columns.bits-column'),
                ViewColumn::make('upload')
                    ->view('tables.columns.bits-column'),
                TextColumn::make('ping'),
                ViewColumn::make('server_id')
                    ->label('Server ID')
                    ->view('tables.columns.server-column'),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y g:ia')
                    ->timezone($settings->timezone ?? 'UTC'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListResults::route('/'),
        ];
    }
}
