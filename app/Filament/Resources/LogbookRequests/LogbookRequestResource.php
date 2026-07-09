<?php

namespace App\Filament\Resources\LogbookRequests;

use App\Filament\Resources\LogbookRequests\Pages\CreateLogbookRequest;
use App\Filament\Resources\LogbookRequests\Pages\EditLogbookRequest;
use App\Filament\Resources\LogbookRequests\Pages\ListLogbookRequests;
use App\Filament\Resources\LogbookRequests\Pages\ViewLogbookRequest;
use App\Filament\Resources\LogbookRequests\Schemas\LogbookRequestForm;
use App\Filament\Resources\LogbookRequests\Schemas\LogbookRequestInfolist;
use App\Filament\Resources\LogbookRequests\Tables\LogbookRequestsTable;
use App\Models\LogbookRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LogbookRequestResource extends Resource
{
    protected static ?string $model = LogbookRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Logbook Management';

    protected static ?string $recordTitleAttribute = 'Transfer Request';

    protected static ?string $navigationLabel = 'Transfer Requests';

    protected static ?string $modelLabel = 'Transfer Requests';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LogbookRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LogbookRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogbookRequestsTable::configure($table);
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
            'index' => ListLogbookRequests::route('/'),
            'create' => CreateLogbookRequest::route('/create'),
            'view' => ViewLogbookRequest::route('/{record}'),
            'edit' => EditLogbookRequest::route('/{record}/edit'),

        ];
    }
}
