<?php

namespace App\Actions\LogbookActions\HelperActions;

use App\Enums\LogBookStatusEnum;
use App\Models\LogbookProfile;

class UpdateLogbookProfileStatusAction
{
    public function __construct(protected LogbookProfile $logbookProfile)
    {
        $this->logbookProfile = $logbookProfile;
    }

    public function handle()
    {

        if (! $this->logbookProfile->status) {
            $this->logbookProfile->update([
                'status' => 1,
            ]);

            $this->logbookProfile->update([
                'status' => 1,
            ]);

            return true;
        }

        if (LogBookStatusEnum::exists($this->logbookProfile->status)) {
            return true;
        }

        $this->logbookProfile->update([
            'status' => 1,
        ]);

        $this->logbookProfile->update([
            'status' => 1,
        ]);
    }
}
