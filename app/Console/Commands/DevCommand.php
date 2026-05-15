<?php

namespace App\Console\Commands;

use App\Actions\LogbookActions\GetChasisInfoAction;
use App\Actions\LogbookActions\ProcessFailedAllocationsAction;
use App\Models\LogbookProfile;
use App\Models\UploadedDataLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


#[Signature('app:dev-command')]
#[Description('Command description')]
class DevCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {

        //     $existinglb = LogbookProfile::withoutGlobalScopes()
        //     ->whereNotNull('DocNum')
        //     ->whereNull('status')
        //     ->where('created_at', '>=', now()->subDays(100))
        //     ->update([
        //         'status' => 1
        //     ]);

        // dd($existinglb);

        $chasis = ' MD625AF44L1AR6249';
        $chasisInfo = (new GetChasisInfoAction($chasis))->handle();

        dd($chasisInfo);


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
