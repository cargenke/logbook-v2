<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportLogs implements FromCollection, WithHeadings
{

    public $uploadresults;
    public $userName;

    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($uploadresults, $userName)
    {
        $this->uploadresults = $uploadresults;
        $this->userName = $userName;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Chasis Number',
            'Reg Number',
            'Status',
            'Remarks',
            'Created On',
            'Created By',
        ];
    }

    public function collection()
    {

        return collect($this->uploadresults)->map(function ($customer) {

            return [
                'Name' => $customer->name,
                'Chasis Number' => $customer->chasisNumber,
                'Reg Number' => $customer->regNumber,
                'Status' => $customer->status,
                'Remarks' => $customer->remarks,
                'Created On' => $customer->createdOn,
                'Created By' => $this->userName,
            ];

        });
    }

}
