<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Storage;

class DeprecatedImage extends Widget
{
    protected static string $view = 'filament.widgets.deprecated-image';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Storage::disk('public')->exists('.deprecated_image');
    }
}
