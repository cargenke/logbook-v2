<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ReceivedLogbooksImport implements ToCollection, WithHeadingRow
{
    public $rows;

    public function collection(Collection $rows)
    {
        $this->rows = $rows->toArray();
    }
}
