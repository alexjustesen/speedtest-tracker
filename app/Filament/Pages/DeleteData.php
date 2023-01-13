<?php

namespace App\Filament\Pages;

use App\Jobs\DeleteResultsData;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;

class DeleteData extends Page
{
    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'system/delete-data';

    protected static string $view = 'filament.pages.delete-data';

    protected ?string $maxContentWidth = '3xl';

    public function getActions(): array
    {
        return [
            Action::make('delete')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->action(fn () => $this->deleteData())
                ->requiresConfirmation()
                ->modalHeading('Confirmation')
                ->modalSubheading('Are you really-really-really sure you want to do this?')
                ->modalContent(view('filament.pages.modals.delete-results-data-confirmation'))
                ->modalButton('Yes, I\'m sure'),
        ];
    }

    protected function deleteData()
    {
        DeleteResultsData::dispatch();

        Notification::make()
            ->title('Deleting speedtest results data')
            ->body('The job has been added to the queue and will be completed shortly.')
            ->warning()
            ->send();
    }
}
