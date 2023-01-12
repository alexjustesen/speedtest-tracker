<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

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
                IconColumn::make('is_successful')
                    ->label('Successful')
                    ->boolean()
                    ->toggleable(),
                IconColumn::make('scheduled')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('download')
                    ->getStateUsing(fn (Result $record): string|null => blank($record->download) ? null : formatBits(formatBytestoBits($record->download), 3).'ps'),
                TextColumn::make('upload')
                    ->getStateUsing(fn (Result $record): string|null => blank($record->upload) ? null : formatBits(formatBytestoBits($record->upload), 3).'ps'),
                TextColumn::make('ping')
                    ->toggleable(),
                TextColumn::make('download_jitter')
                    ->getStateUsing(fn (Result $record): string|null => json_decode($record->data, true)['download']['latency']['jitter'] ?? null)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('upload_jitter')
                    ->getStateUsing(fn (Result $record): string|null => json_decode($record->data, true)['upload']['latency']['jitter'] ?? null)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('ping_jitter')
                    ->getStateUsing(fn (Result $record): string|null => json_decode($record->data, true)['ping']['jitter'] ?? null)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('server')
                    ->getStateUsing(fn (Result $record): string|null => ! blank($record->server_id) ? $record->server_id.' ('.$record->server_name.')' : null)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime($settings->time_format ?? 'M j, Y G:i:s')
                    ->timezone($settings->timezone ?? 'UTC'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('view result')
                        ->label('View on Speedtest.net')
                        ->icon('heroicon-o-link')
                        ->url(fn (Result $record): string|null => optional($record)->url)
                        ->hidden(fn (Result $record): bool => ! $record->is_successful)
                        ->openUrlInNewTab(),
                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
