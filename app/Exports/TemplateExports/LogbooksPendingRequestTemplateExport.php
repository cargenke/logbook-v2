<?php

namespace App\Exports\TemplateExports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogbooksPendingRequestTemplateExport implements FromCollection, WithHeadings
{
    public $logbooks;

    public function __construct(array $logbooks)
    {
        // Convert array to collection
        $this->logbooks = collect($logbooks);
    }

    public function collection()
    {
        return $this->logbooks;
    }

    public function headings(): array
    {

        $this->logbooks;
        return [
            'chasis_number',
            'reg_number',
            'status',
        ];
    }
}
