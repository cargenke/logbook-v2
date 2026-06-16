<?php

namespace App\Console\Commands;

use App\Actions\LogbookActions\GetChasisInfoAction;
use App\Actions\LogbookActions\ProcessFailedAllocationsAction;
use App\Enums\LogBookStatusEnum;
use App\Exports\PendingAcceptanceNotificationExport;
use App\Mail\PendingAcceptanceNotificationMail;
use App\Models\LogbookProfile;
use App\Models\UploadedDataLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;


#[Signature('app:dev-command')]
#[Description('Command description')]
class DevCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {

          $user = Auth::loginUsingId(1);

    $logbookInformation = LogbookProfile::where('chasisNumber','MD625GF59F1A74778')->first();

    dd($logbookInformation);



        $date = now();


        Mail::to('caroline.akinyi@cargen.com')
            ->cc(['kenneth.kibet@cargen.com', 'joyleen.lubanga@cargen.com', 'gideon.yegon@cargen.com'])
            ->send(new PendingAcceptanceNotificationMail(LogBookStatusEnum::PENDING_ACCEPTANCE));

        dd('Exported successfully');

        $this->info('Daily Beyond Cap Report Notification Sent  . Date: ' . $date);

        $date = now()->format('Y-m-d');

        $fileName = "beyond_cap_report_{$date}.xlsx";
        Excel::store(new PendingAcceptanceNotificationExport(LogBookStatusEnum::PENDING_ACCEPTANCE), $fileName);


        dd('Exported successfully');
        //     $existinglb = LogbookProfile::withoutGlobalScopes()
        //     ->whereNotNull('DocNum')
        //     ->whereNull('status')
        //     ->where('created_at', '>=', now()->subDays(100))
        //     ->update([
        //         'status' => 1
        //     ]);

        // dd($existinglb);




        $chasiss = UploadedDataLog::doesntHave("profile")
            ->where('name', 'Received LogBook/Allocation')
            ->where('status', 'Failed')
            ->where('created_at', '>=', now()->subDays(60))
            ->distinct()
            ->pluck('chasisNumber');

        foreach ($chasiss as $chasis) {


            try {
                Log::info('Processing Chasisi: ' . $chasis);
                $chasisInfo = (new GetChasisInfoAction($chasis))->handle();

                if (!$chasisInfo) {
                    Log::info('Chasis info not found for: ' . $chasis);
                    continue;
                }

                (new ProcessFailedAllocationsAction($chasis))->handle();


            } catch (\Exception $e) {
                Log::error('Error processing chasis: ' . $chasis . ' Error: ' . $e->getMessage());
                continue; // Skip to the next chasis


            }
        }
    }
}
