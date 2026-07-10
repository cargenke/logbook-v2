<?php

namespace App\Actions\LogbookActions;

use App\Actions\LogbookActions\HelperActions\UpdateLogbookProfileStatusAction;
use App\Models\Logbook;
use App\Models\LogbookProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateLogbookInfoAction
{
    public function __construct(protected array $logbook)
    {
        $this->logbook = $logbook;
    }

    public function handle()
    {
        $logbook = $this->logbook;

        $logbookProfile = LogbookProfile::withoutGlobalScopes()
            ->where('chasisNumber', $logbook['DistNumber'])
            ->first();

        if (! $logbookProfile) {

            Log::info('DistNumber: '.$logbook['DistNumber'].' - No existing logbook profile found. Creating new logbook and profile.');

            $lb = Logbook::withoutGlobalScopes()
                ->updateOrCreate(
                    [
                        'chasisNumber' => $logbook['DistNumber'],
                    ],
                    [
                        'createdOn' => Carbon::now(),
                        'status' => 1,
                        'pendingRequestsCreatedOn' => Carbon::now(),
                    ]
                );

            $logbookProfile = LogbookProfile::withoutGlobalScopes()
                ->updateOrCreate(
                    [
                        'chasisNumber' => $logbook['DistNumber'],
                    ],
                    [
                        'logbook_id' => $lb->id,
                        'CardCode' => $logbook['CardCode'],
                        'CustomerName' => $logbook['CustomerName'],
                        'DocNum' => $logbook['DocNum'],
                        'Location' => $logbook['Location'],
                        'PinNo' => $logbook['PINNo'],
                        'IDNo' => $logbook['IDNo'],
                        'LogBookFee' => $logbook['LogBookFee'],
                        'U_ProdLine' => $logbook['U_ProdLine'],
                        'DocDate' => substr($logbook['DocDate'], 0, 16),
                        'NumAtCard' => $logbook['NumAtCard'],
                        'tel' => $logbook['PhoneNumber'],
                        'status' => 1,
                        'createdOn' => Carbon::now(),
                    ]
                );
        }
        (new UpdateLogbookProfileStatusAction($logbookProfile))->handle();
        (new ProcessFailedAllocationsAction($logbook['DistNumber']))->handle();
    }
}
