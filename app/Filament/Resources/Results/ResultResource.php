<?php

namespace App\Filament\Resources\Results;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use App\Filament\Resources\Results\Pages\ListResults;
use App\Enums\ResultStatus;
use App\Filament\Exports\ResultExporter;
use App\Filament\Resources\Results\Pages;
use App\Helpers\Number;
use App\Jobs\TruncateResults;
use App\Models\Result;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use App\Filament\Resources\Results\Schemas\ResultForm;
use App\Filament\Resources\Results\Tables\ResultTable;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ResultForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ResultTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResults::route('/'),
        ];
    }
}
