<?php

namespace App\Jobs;

use App\Mail\LogbookRequestPendingAcceptanceMail;
use App\Models\LogbookRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLogbookRequestPendingAcceptanceNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public LogbookRequest $logbookRequest)
    {
    }

    public function handle(): void
    {
        $creator = $this->logbookRequest->user;

        if (!$creator?->email) {
            return;
        }

        Mail::to($creator->email)
            ->bcc('devops@cargen.com')
            ->send(new LogbookRequestPendingAcceptanceMail($this->logbookRequest));
    }
}