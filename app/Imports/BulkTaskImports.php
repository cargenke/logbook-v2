<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BulkTaskImports implements ToCollection, WithHeadingRow, WithChunkReading
{
    public $rows;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            dd($row);
            $this->data[] = [
                'chasisNumber' => $row['name'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
            ];
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
