<?php

namespace App\Actions\LogbookActions\HelperActions;

use App\Enums\LogBookStatusEnum;
use App\Models\LogbookProfile;
use Carbon\Carbon;

class UpdateLogbookProfileStatusAction
{
    public function __construct(protected LogbookProfile $logbookProfile)
    {
        $this->logbookProfile = $logbookProfile;
    }

    public function handle()
    {

        if (LogBookStatusEnum::exists($this->logbookProfile->status)) {
            return true;
        }

        $this->logbookProfile->update([
            'status' => 1
        ]);

        $this->logbookProfile->update([
            'status' => 1
        ]);
    }
}
