<?php

namespace App\Actions\LogbookActions;

use App\Models\Logbook;
use App\Models\LogbookProfile;
use App\Models\UploadedDataLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessFailedAllocationsAction
{
    public function __construct(protected string $chasisNumber)
    {
        $this->chasisNumber = $chasisNumber;
    }

    public function handle()
    {
        $chasisNumber = $this->chasisNumber;

        $log = UploadedDataLog::where('name', 'Received LogBook/Allocation')
            ->where('chasisNumber', $chasisNumber)
            ->where('status', 'Failed')
            ->first();

        if (! $log) {
            Log::info('(DONE ON SALES/STOCK SYNC) There is no Failed Allocation '.$chasisNumber.' '.$log?->regNumber);

            return false;
        }

        $updateValues = false;

        $logbookWithNullReg = Logbook::withoutGlobalScopes()->where('chasisNumber', $chasisNumber)
            ->whereNull('regNumber')
            ->first();

        if ($logbookWithNullReg) {
            $updateValues = true;
        }

        $LogbookProfileWithNull = LogbookProfile::withoutGlobalScopes()
            ->where('chasisNumber', $chasisNumber)
            ->whereNull('regNumber')
            ->first();

        if ($LogbookProfileWithNull) {
            $updateValues = true;
        }

        if (! $updateValues) {
            return false;
        }

        Logbook::withoutGlobalScopes()->where('chasisNumber', $chasisNumber)
            ->update([
                'regNumber' => $log->regNumber,
            ]);

        LogbookProfile::withoutGlobalScopes()->where('chasisNumber', $chasisNumber)
            ->update([
                'regNumber' => $log?->regNumber,
            ]);

        UploadedDataLog::create(
            [
                'name' => 'Received LogBook/Allocation',
                'chasisNumber' => $chasisNumber,
                'regNumber' => $log?->regNumber,
                'status' => 'Success',
                'remarks' => 'Allocation Successfull',
                'createdOn' => Carbon::now(),
                'createdBy' => 1,
            ]
        );

    }
}
