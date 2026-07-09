<?php

namespace App\Exports\TemplateExports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AvailablePlatesTemplateExport implements FromArray, WithHeadings
{
    protected $plates;

    public function __construct($plates)
    {
        $this->plates = $plates;
    }

    public function array(): array
    {
        return $this->plates;
    }

    public function headings(): array
    {
        return [
            'registration number',
        ];
    }
}
