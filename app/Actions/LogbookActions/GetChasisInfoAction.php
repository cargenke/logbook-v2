<?php

namespace App\Actions\LogbookActions;

use App\Services\BridgeServiceProvider;

class GetChasisInfoAction
{
    public function __construct(protected string $chasisNumber)
    {
        $this->chasisNumber = $chasisNumber;
    }

    public function handle()
    {

        $chasisNumber = $this->chasisNumber;

        $payload = [
            'chasisNumber' => $chasisNumber,
        ];

        $chasisInfo = (new BridgeServiceProvider)->postData('/chasis-sales-info', $payload);

        return $chasisInfo;

    }
}
