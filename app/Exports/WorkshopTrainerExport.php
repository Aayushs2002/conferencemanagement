<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkshopTrainerExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $trainers;
    protected $workshopTitle;

    public function __construct($trainers, $workshopTitle = null)
    {
        $this->trainers = $trainers;
        $this->workshopTitle = $workshopTitle;
    }

    public function collection()
    {
        $arrayData = [];
        $counter = 1;
        foreach ($this->trainers as $trainer) {
            // Skip trainers with null user_id
            if (empty($trainer->user_id) || !$trainer->user) {
                continue;
            }

            $arrayData[] = [
                'S.No.' => $counter++,
                'Workshop' => $trainer->workshop->workshop_title ?? 'N/A',
                'Name' => $trainer->user?->fullName($trainer->user) ?? 'N/A',
                'Email' => $trainer->user?->email ?? 'N/A',
                'Phone' => $trainer->user?->userDetail?->phone ?? 'N/A',
                'Council Number' => $trainer->user?->userDetail?->council_number ?? 'N/A',
                'Institution' => $trainer->user?->userDetail?->institution?->name ?? 'N/A',
                'Country' => $trainer->user?->userDetail?->country?->country_name ?? 'N/A',
                'Verified Status' => $trainer->verified_status == 1 ? 'Verified' : ($trainer->verified_status == 2 ? 'Rejected' : 'Pending'),
                'Registration Date' => $trainer->created_at->format('d-M-Y H:i A'),
            ];
        }
        return collect($arrayData);
    }

    public function headings(): array
    {
        return [
            "S.No.", 
            "Workshop", 
            "Name", 
            "Email", 
            "Phone", 
            "Medical Council Number", 
            "Institution",
            "Country", 
            "Verified Status",
            "Registration Date"
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
