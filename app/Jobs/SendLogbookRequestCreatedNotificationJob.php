<?php

namespace App\Jobs;

use App\Mail\LogbookRequestCreatedMail;
use App\Models\LogbookRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLogbookRequestCreatedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public LogbookRequest $logbookRequest)
    {
    }

    public function handle(): void
    {
        // $recipients = collect(explode(',', (string) config('services.logbook_request_notification.recipients')))
        //     ->map(fn(string $email) => trim($email))
        //     ->filter(fn(string $email) => filter_var($email, FILTER_VALIDATE_EMAIL))
        //     ->values()
        //     ->all();



        // if ($recipients === []) {
        //     return;
        // }

        Mail::to(['registration@cargen.com', 'sevanne.wesah@cargen.org', 'melvine.auma@cargen.org', 'caroline.akinyi@cargen.com'])
            ->bcc(['devops@cargen.org'])
            ->send(new LogbookRequestCreatedMail($this->logbookRequest));
    }
}