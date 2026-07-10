<?php

namespace App\Filament\Resources\LogbookProfiles;

use App\Filament\Resources\LogbookProfiles\Pages\CreateLogbookProfile;
use App\Filament\Resources\LogbookProfiles\Pages\ListLogbookProfiles;
use App\Filament\Resources\LogbookProfiles\Pages\LogbookInfo;
use App\Filament\Resources\LogbookProfiles\Pages\ViewLogbookProfile;
use App\Filament\Resources\LogbookProfiles\Schemas\LogbookProfileForm;
use App\Filament\Resources\LogbookProfiles\Schemas\LogbookProfileInfolist;
use App\Filament\Resources\LogbookProfiles\Tables\LogbookProfilesTable;
use App\Models\LogbookProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LogbookProfileResource extends Resource
{
    protected static ?string $model = LogbookProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PaperClip;

    protected static string|UnitEnum|null $navigationGroup = 'Logbook Management';

    protected static ?string $recordTitleAttribute = 'Logbooks';

    protected static ?string $navigationLabel = 'Logbooks';

    protected static ?string $modelLabel = 'Logbooks';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return LogbookProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LogbookProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogbookProfilesTable::configure($table);
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
            'index' => ListLogbookProfiles::route('/'),
            'create' => CreateLogbookProfile::route('/create'),
            'view' => ViewLogbookProfile::route('/{record}'),
            'info' => LogbookInfo::route('/{record}/info'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('id', function ($q) {
                $q->select('id')
                    ->from(function ($sub) {
                        $sub->from('logbook_profiles')
                            ->selectRaw('MIN(id) as id')
                            ->groupBy('chasisNumber');
                    }, 'unique_logbooks');
            });
    }
}
