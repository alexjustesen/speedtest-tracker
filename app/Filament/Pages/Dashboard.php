<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-layout-dashboard';

    protected string $view = 'filament.pages.dashboard';

    public function getTitle(): string
    {
        return __('dashboard.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.title');
    }
}
