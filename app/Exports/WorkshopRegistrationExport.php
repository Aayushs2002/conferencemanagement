<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkshopRegistrationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $registrants;
    protected $workshopTitle;

    public function __construct($registrants, $workshopTitle = null)
    {
        $this->registrants = $registrants;
        $this->workshopTitle = $workshopTitle;
    }

    public function collection()
    {
        $arrayData = [];
        $counter = 1;
        foreach ($this->registrants as $registrant) {
            // Skip registrants with null user_id
            if (empty($registrant->user_id) || !$registrant->user) {
                continue;
            }

            $arrayData[] = [
                'S.No.' => $counter++,
                'Workshop' => $registrant->workshop->workshop_title ?? 'N/A',
                'Name' => $registrant->user?->fullName($registrant->user) ?? 'N/A',
                'Email' => $registrant->user?->email ?? 'N/A',
                'Phone' => $registrant->user?->userDetail?->phone ?? 'N/A',
                'Council Number' => $registrant->user?->userDetail?->council_number ?? 'N/A',
                'Institution' => $registrant->user?->userDetail?->institution?->name ?? 'N/A',
                'Country' => $registrant->user?->userDetail?->country?->country_name ?? 'N/A',
                'Meal Type' => $registrant->meal_type == 1 ? 'Vegetarian' : ($registrant->meal_type == 2 ? 'Non-Vegetarian' : 'N/A'),
                'Amount' => $registrant->amount ?? '0',
                'Transaction ID' => $registrant->transaction_id ?? 'N/A',
                'Verified Status' => $registrant->verified_status == 1 ? 'Verified' : ($registrant->verified_status == 2 ? 'Rejected' : 'Pending'),
                'Registration Date' => $registrant->created_at->format('d-M-Y H:i A'),
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
            "Meal Type", 
            "Amount", 
            "Transaction ID", 
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
