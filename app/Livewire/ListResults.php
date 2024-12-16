<?php

namespace App\Livewire;

use App\Enums\ResultStatus;
use App\Models\Result;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ListResults extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Result::query()->whereDate('created_at', '>=', now()->subDays(30)))
            ->poll('5s')
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data.server.id')
                    ->label('Server ID'),

                Tables\Columns\TextColumn::make('data.server.name')
                    ->label('Server name')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.interface.externalIp')
                    ->label('External IP address')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('download')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('upload')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ping')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('data.download.latency.jitter')
                    ->label('Download jitter')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.download.latency.high')
                    ->label('Download latency high')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.download.latency.low')
                    ->label('Download latency low')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.download.latency.iqm')
                    ->label('Download latency iqm')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.upload.latency.jitter')
                    ->label('Upload jitter')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.upload.latency.high')
                    ->label('Upload latency high')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.upload.latency.low')
                    ->label('Upload latency low')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.upload.latency.iqm')
                    ->label('Upload latency iqm')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.ping.jitter')
                    ->label('Ping jitter')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data.packetLoss')
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 2, '.', '').' %';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\IconColumn::make('scheduled')
                    ->alignCenter()
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // TODO: external IP address

                TernaryFilter::make('scheduled'),

                Filter::make('server_id')
                    ->form([
                        TagsInput::make('server_ids'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['server_ids'],
                                fn (Builder $query): Builder => $query->whereIn('data->server->id', $data['server_ids']),
                            );
                    }),

                SelectFilter::make('status')
                    ->multiple()
                    ->options(ResultStatus::class),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.list-results');
    }
}
