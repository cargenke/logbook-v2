<?php

namespace App\Exports\TemplateExports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class AllUploadTemplateExport implements  WithHeadings
{
    public $headings = [];

    public function __construct(array $headings)
    {
        // Convert array to collection
        $this->headings = $headings;
    }


    public function headings(): array
    {

        return $this->headings;
    }
}
