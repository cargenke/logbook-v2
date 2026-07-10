<?php

namespace App\Console\Commands;

use App\Enums\LogBookStatusEnum;
use App\Mail\PendingAcceptanceNotificationMail;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
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

        Mail::to(['carol.akinyi@cargen.com','sevanne.wesah@cargen.org'])
            ->bcc('devops@cargen.com')
            ->send(new PendingAcceptanceNotificationMail(LogBookStatusEnum::PENDING_ACCEPTANCE));

    }
}
