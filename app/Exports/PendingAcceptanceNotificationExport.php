<?php

namespace App\Exports;

use App\Enums\LogBookStatusEnum;
use App\Models\LogbookProfile;
use App\Models\LogbookRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendingAcceptanceNotificationExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected Collection $records;

    public function __construct(protected LogBookStatusEnum $status)
    {
        $this->status = $status;
    }

    public function collection()
    {

        $logbooks = LogbookRequest::select(
            'chasisNumber',
            'regNumber',
            'createdOn'
        )
            ->where('status', $this->status)
            ->get();

        return $this->records = $logbooks;
    }

    public function headings(): array
    {
        return [
            'Chasis Number',
            'Registration Number',
            'Created On',
        ];
    }

    public function map($record): array
    {

        return [
            $record->chasisNumber,
            $record->regNumber,
            $record->createdOn,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],

            // Add borders to all cells
            'A1:C' . ($this->records->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}
