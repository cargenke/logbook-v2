<?php

namespace App\Actions\LogbookActions;

use App\Actions\LogbookActions\ProcessFailedAllocationsAction;
use App\Models\Logbook;
use App\Models\LogbookProfile;
use App\Models\UploadedDataLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateLogbookInfoAction
{

    public function __construct(protected array $logbook)
    {
        $this->logbook = $logbook;
    }

    function handle()
    {
        $logbook = $this->logbook;


        $existinglb = LogbookProfile::where('chasisNumber', $logbook['DistNumber'])->first();


        if (!$existinglb) {

            Log::info('DistNumber: ' . $logbook['DistNumber'] . ' - No existing logbook profile found. Creating new logbook and profile.');

            $lb = Logbook::updateOrCreate(
                [
                    'chasisNumber' => $logbook['DistNumber'],
                ],
                [
                    // 'regNumber' => $logbook['NumberPlate'] ?? null,
                    'createdOn' => Carbon::now(),
                    'status' => 1,
                    'pendingRequestsCreatedOn' => Carbon::now(),
                ]
            );

            $logbookn = LogbookProfile::updateOrCreate(
                [
                    'chasisNumber' => $logbook['DistNumber'],
                ],
                [
                    'logbook_id' => $lb->id,
                    // 'regNumber' => $logbook['NumberPlate'],
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

        if ($existinglb && !$existinglb->status) {

            Logbook::where('chasisNumber', $logbook['DistNumber'])
                ->update([
                    'status' => 1
                ]);

            LogbookProfile::where('chasisNumber', $logbook['DistNumber'])->update([
                'status' => 1
            ]);
        }


        if ($existinglb && $existinglb->status == null) {
            Logbook::where('chasisNumber', $logbook['DistNumber'])
                ->update([
                    'status' => 1
                ]);

            LogbookProfile::where('chasisNumber', $logbook['DistNumber'])->update([
                'status' => 1
            ]);
        }

        (new ProcessFailedAllocationsAction($logbook['DistNumber']))->handle();



    }
}
