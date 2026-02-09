<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SponsorExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $sponsors;

    public function __construct($sponsors)
    {
        $this->sponsors = $sponsors;
    }

    public function collection()
    {
        $arrayData = [];
        foreach ($this->sponsors as $key => $sponsor) {
            $arrayData[] = [
                'S.No.' => $key + 1,
                'Category' => $sponsor->category->category_name ?? '-',
                'Name' => $sponsor->name ?? '-',
                'Amount' => $sponsor->amount ?? '-',
                'Address' => $sponsor->address ?? '-',
                'Contact Person' => $sponsor->contact_person ?? '-',
                'Email' => $sponsor->email ?? '-',
                'Phone' => $sponsor->phone ?? '-',
                'Total Participants' => $sponsor->total_attendee ?? 0,
                'Published' => $sponsor->visible_status == 1 ? 'Yes' : 'No',
            ];
        }
        return collect($arrayData);
    }

    public function headings(): array
    {
        return [
            "S.No.",
            "Category",
            "Name",
            "Amount",
            "Address",
            "Contact Person",
            "Email",
            "Phone",
            "Total Participants",
            "Published"
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
