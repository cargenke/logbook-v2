<?php

namespace App\Filament\Resources\LogbookProfiles\Pages;

use App\Filament\Resources\LogbookProfiles\LogbookProfileResource;
use App\Filament\Resources\LogbookProfiles\Widgets\LogbookStatOverView;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListLogbookProfiles extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = LogbookProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LogbookStatOverView::class,
        ];
    }
}
