<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ImportLogExport implements FromArray, WithHeadings
{
    protected $logs;

    public function __construct(array $logs)
    {
        $this->logs = $logs;
    }

    public function array(): array
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return ['Row Number', 'Reason'];
    }
}
