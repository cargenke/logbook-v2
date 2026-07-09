<?php

namespace App\Filament\Resources\LogbookProfiles\Pages;

use App\Filament\Resources\LogbookProfiles\LogbookProfileResource;
use App\Models\LogbookRequest;
use Filament\Resources\Pages\ViewRecord;

class ViewLogbookProfile extends ViewRecord
{
    protected static string $resource = LogbookProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $logBookRequest = LogbookRequest::where('chasisNumber', $this->record->chasisNumber)
            ->first();

        if ($logBookRequest) {
            $data['name1'] = $logBookRequest->name1;
            $data['tel1'] = $logBookRequest->tel1;
            $data['tel2'] = $logBookRequest->tel2;
            $data['email'] = $logBookRequest->email;
            $data['PinNo1'] = $logBookRequest->PinNo1;
            $data['modeofpayment'] = $logBookRequest->modeofpayment;
            $data['name2'] = $logBookRequest->name2;
            $data['PinNo2'] = $logBookRequest->PinNo2;
            $data['PinNo3'] = $logBookRequest->PinNo3;
        }

        return $data;

    }
}
