<?php

namespace App\Filament\Pages;

use App\Models\Backup as ModelsBackup;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;

class Backup extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-archive';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'system/backup';

    protected static string $view = 'filament.pages.backup';

    protected function getActions(): array
    {
        return [
            Action::make('backup')
                ->label('Queue backup')
                ->action('queueBackupJob'),

            ActionGroup::make([
                Action::make('cleanup')
                    ->label('Cleanup existing backups')
                    ->icon('heroicon-o-folder-remove')
                    ->action('queueBackupCleanupJob'),
            ])
        ];
    }

    // TODO store backups in the database so that they can be queried
    protected function getTableQuery()
    {
        return ModelsBackup::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('created_at'),
        ];
    }

    public function queueBackupTask(): void
    {
        Notification::make()
            ->title('Backup task queued.')
            ->success()
            ->send();
    }
}
