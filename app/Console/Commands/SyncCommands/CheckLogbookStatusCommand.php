<?php

namespace App\Console\Commands\SyncCommands;

use App\Enums\LogBookStatusEnum;
use App\Models\LogbookProfile;
use App\Models\LogbookRequest;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

#[Signature('app:check-logbook-status')]
#[Description('Check logbook status')]
class CheckLogbookStatusCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = Auth::loginUsingId(78);

        $logbooks = LogbookProfile::doesntHave('request')
            ->where('status', LogBookStatusEnum::PENDING_ACCEPTANCE)
            ->update([
                    'status' => LogBookStatusEnum::PENDING->value,
                ]);


     
        $logbookWithPendingAcceptance = LogbookProfile::doesntHave('request')
            ->where('status', LogBookStatusEnum::PENDING_ACCEPTANCE)->count();


        $this->comment('Logbook With Pending Acceptance ' . $logbookWithPendingAcceptance);



        $LogbookRequest = LogbookRequest::where('status', LogBookStatusEnum::PENDING_ACCEPTANCE->value)->count();


        $this->info('Logbook Request with Pending Acceptance status for: ' . $LogbookRequest);

        $totalLogbook = LogbookProfile::where('status', LogBookStatusEnum::PENDING_ACCEPTANCE)->each(function ($logbook) use ($user) {

            $logbookRequest = LogbookRequest::where('chasisNumber', $logbook->chasisNumber)->first();

            if (!$logbookRequest) {
                $logbook->update([
                    'status' => LogBookStatusEnum::PENDING->value,
                ]);
                return;
            }

            if ($logbook->status == $logbookRequest->status) {
                return;
            }


            LogbookRequest::where('chasisNumber', $logbook->chasisNumber)
                ->update([
                    'status' => $logbook->status,
                ]);

        });


        $LogbookRequest = LogbookRequest::where('status', LogBookStatusEnum::PENDING_ACCEPTANCE->value)->count();

        $this->info('Updating status for: ' . $LogbookRequest);


    }
}
