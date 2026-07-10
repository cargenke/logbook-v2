<?php

namespace App\Exports\TemplateExports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogbooksRequestTemplateExport implements FromCollection, WithHeadings
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
        return [
            'chasis_number',
            'reg_number',
            'name1',
            'name2',
            'kra_pin1',
            'kra_pin2',
            'kra_pin3',
            'phone_number1',
            'phone_number2',
            'payment_mode',
            'email',
            'branch',
            'status',
            'dealer_id',
        ];
    }
}
