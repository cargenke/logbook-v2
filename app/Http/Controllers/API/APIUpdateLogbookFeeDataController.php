<?php

namespace App\Http\Controllers\API;

use App\Actions\LogbookActions\ProcessFailedAllocationsAction;
use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\LogbookProfile;
use App\Services\ApiResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class APIUpdateLogbookFeeDataController extends Controller
{
    public function __invoke(Request $request)
    {

        try {

            $logbooks = collect($request['logbookData']);

            foreach ($logbooks as $key => $val) {

                $exlb = LogbookProfile::where('chasisNumber', $val['DistNumber'])
                    ->whereNotNull('DocNum')
                    ->first();

                if (! $exlb) {
                    $lb = Logbook::firstOrCreate(
                        [
                            'chasisNumber' => $val['DistNumber'],
                        ],
                        [

                            'createdOn' => Carbon::now(),
                            'pendingRequestsCreatedOn' => Carbon::now(),
                        ]
                    );

                    LogbookProfile::firstOrCreate(
                        [
                            'chasisNumber' => $val['DistNumber'],
                        ],
                        [
                            'logbook_id' => $lb->id,
                            // 'regNumber' => $val['NumberPlate'],
                            'CardCode' => $val['CardCode'],
                            'CustomerName' => $val['CustomerName'],
                            'DocNum' => $val['DocNum'],
                            'Location' => $val['Location'],
                            'PinNo' => $val['PINNo'],
                            'IDNo' => $val['IDNo'],
                            'LogBookFee' => $val['LogBookFee'],
                            'U_ProdLine' => $val['U_ProdLine'],
                            'regNumber' => $val['NumberPlate'],
                            'DocDate' => substr($val['DocDate'], 0, 16),
                            'NumAtCard' => $val['NumAtCard'],
                            'tel' => $val['PhoneNumber'],
                            'ItemCode' => $val['ItemCode'],
                            'createdOn' => Carbon::now(),
                        ]
                    );
                }

                if ($exlb && ! $exlb->status) {

                    Logbook::where('chasisNumber', $val['DistNumber'])->update([
                        'status' => 1,
                    ]);

                    LogbookProfile::where('chasisNumber', $val['DistNumber'])->update([
                        'status' => 1,
                    ]);
                }

                (new ProcessFailedAllocationsAction($val['DistNumber']))->handle();
            }

            return (new ApiResponseService)->apiSucccessResponse('Created');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());

            return (new ApiResponseService)->apiFailedResponse($th->getMessage());
        }
    }
}
