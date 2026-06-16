<?php

namespace App\Http\Controllers\API;


use App\Actions\LogbookActions\UpdateLogbookInfoAction;
use App\Http\Controllers\Controller;
use App\Models\LogbookProfile;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class APILogbookDataController extends Controller
{
    public function __invoke(Request $request)
    {
        try {

            $logbooks = collect($request['logbookData']);

            foreach ($logbooks as $key => $val) {

                $exlb = LogbookProfile::where('chasisNumber', $val['DistNumber'])
                    ->whereNotNull('DocNum')
                    ->first();

                if (!$exlb) {
                    (new UpdateLogbookInfoAction($val))->handle();
                }

            }

            return (new ApiResponseService())->apiSucccessResponse("Created");
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return (new ApiResponseService())->apiFailedResponse($th->getMessage());
        }
    }
}
