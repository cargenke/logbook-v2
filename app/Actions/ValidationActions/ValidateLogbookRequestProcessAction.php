<?php

namespace App\Actions\ValidationActions;

class ValidateLogbookRequestProcessAction
{
    public function __construct(protected string $chasisNumber)
    {
        $this->chasisNumber = $chasisNumber;
    }

    public function handle(): bool
    {

        return true;

    }
}
