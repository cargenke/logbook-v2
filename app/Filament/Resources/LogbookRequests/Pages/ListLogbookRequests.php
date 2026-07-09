<?php

namespace App\Filament\Resources\LogbookRequests\Pages;

use App\Filament\Resources\LogbookRequests\LogbookRequestResource;
use App\Filament\Resources\LogbookRequests\Widgets\LogbookRequestStatOverView;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListLogbookRequests extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = LogbookRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LogbookRequestStatOverView::class,
        ];
    }
}
