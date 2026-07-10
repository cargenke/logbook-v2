<?php

namespace App\Filament\Resources\LogbookRequests\Pages;

use App\Filament\Resources\LogbookRequests\LogbookRequestResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLogbookRequest extends EditRecord
{
    protected static string $resource = LogbookRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
