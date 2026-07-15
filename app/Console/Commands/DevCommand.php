<?php

namespace App\Console\Commands;

use App\Actions\LogbookActions\GetChasisInfoAction;
use App\Models\LogbookProfile;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

#[Signature('app:dev-command')]
#[Description('Command description')]
class DevCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {

    

        $user = Auth::loginUsingId(12);

        $logbookWithoutTransferFee = LogbookProfile::where('LogBookFee', '<=', 0)
            ->where('created_at', '>', now()->subMonths(6))
            ->get();


        foreach ($logbookWithoutTransferFee as $key => $logbook) {

            $logbookInfo = (new GetChasisInfoAction($logbook->chasisNumber))->handle();

            if (!$logbookInfo) {
                $this->info('No info for: ' . $logbook->chasisNumber);
                continue;
            }

            $logbook->update([
                'LogBookFee' => $logbookInfo['LogBookFee'],
            ]);

            $this->comment('Updated: ' . $logbook->chasisNumber);
        

        }


        // Mail::to(['carol.akinyi@cargen.com', 'sevanne.wesah@cargen.org'])
        //     ->bcc('devops@cargen.com')
        //     ->send(new PendingAcceptanceNotificationMail(LogBookStatusEnum::PENDING_ACCEPTANCE));

    }
}
