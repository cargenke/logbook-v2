<?php

namespace App\Filament\Resources\LogbookProfiles\Pages;

use App\Filament\Resources\LogbookProfiles\LogbookProfileResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLogbookProfile extends EditRecord
{
    protected static string $resource = LogbookProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

        ];
    }
}
