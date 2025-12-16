<?php

namespace App\Filament\Pages\Tools;

use App\Actions\GetOoklaSpeedtestServers;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class ListOoklaServers extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Tools';

    protected static ?string $title = 'List Ookla Servers';

    protected static ?string $navigationLabel = 'List Ookla Servers';

    protected string $view = 'filament.pages.tools.list-ookla-servers';

    protected static ?string $slug = 'tools/list-ookla-servers';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?string $servers = null;

    public bool $isLoading = true;

    public function mount(): void
    {
        $this->fetchServers();
    }

    public function fetchServers(): void
    {
        $this->isLoading = true;

        try {
            $servers = GetOoklaSpeedtestServers::fetch();

            $this->servers = json_encode($servers, JSON_PRETTY_PRINT);

            $this->form->fill([
                'servers' => $this->servers,
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('errors.error_fetching_servers'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->servers = '';
            $this->form->fill([
                'servers' => '',
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('servers')
                    ->label(false)
                    ->rows(20)
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'font-mono'])
                    ->disabled(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refreshServers')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->fetchServers();

                    Notification::make()
                        ->title(__('errors.servers_refreshed_successfully'))
                        ->success()
                        ->send();
                }),

            Action::make('copyServers')
                ->label('Copy to Clipboard')
                ->icon('heroicon-o-clipboard')
                ->disabled(fn () => blank($this->servers))
                ->requiresConfirmation(false)
                ->action(function () {
                    $this->js('navigator.clipboard.writeText('.json_encode($this->servers).')');

                    Notification::make()
                        ->title(__('errors.copied_to_clipboard'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
