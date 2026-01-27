<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class InternationalAccommodationExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $accommodations;
    protected $conference;
    protected $rowNumber = 0;

    public function __construct($accommodations, $conference)
    {
        $this->accommodations = $accommodations;
        $this->conference = $conference;
    }

    public function collection()
    {
        return $this->accommodations;
    }

    public function headings(): array
    {
        return [
            '#',
            'Participant Name',
            'Email',
            'Phone',
            'Country',
            'Hotel Name',
            'Hotel Address',
            'Room Type',
            'Flight Number',
            'Arrival Date',
            'Arrival Time',
            'Departure Date',
            'Departure Time',
            'Check-in Date',
            'Check-out Date',
            'Stay Duration (Nights)',
            'Airport Pickup Required',
            'Special Requirements',
            'Created By',
            'Created At',
            'Last Updated',
        ];
    }

    public function map($accommodation): array
    {
        $this->rowNumber++;
        
        $checkInDate = $accommodation->check_in_date ? Carbon::parse($accommodation->check_in_date) : null;
        $checkOutDate = $accommodation->check_out_date ? Carbon::parse($accommodation->check_out_date) : null;
        $stayDuration = ($checkInDate && $checkOutDate) ? $checkInDate->diffInDays($checkOutDate) : 'N/A';

        return [
            $this->rowNumber,
            $accommodation->conferenceRegistration->user->f_name . ' ' . $accommodation->conferenceRegistration->user->l_name,
            $accommodation->conferenceRegistration->user->email,
            $accommodation->conferenceRegistration->user->userDetail->phone ?? 'N/A',
            $accommodation->conferenceRegistration->user->userDetail->country->country_name ?? 'N/A',
            $accommodation->hotel->name ?? 'N/A',
            $accommodation->hotel->address ?? 'N/A',
            $accommodation->room_type ? ucfirst($accommodation->room_type) : 'Not specified',
            $accommodation->flight_number ?? 'N/A',
            $accommodation->arrival_date ? Carbon::parse($accommodation->arrival_date)->format('M d, Y') : 'N/A',
            $accommodation->arrival_time ? Carbon::parse($accommodation->arrival_time)->format('h:i A') : 'N/A',
            $accommodation->departure_date ? Carbon::parse($accommodation->departure_date)->format('M d, Y') : 'N/A',
            $accommodation->departure_time ? Carbon::parse($accommodation->departure_time)->format('h:i A') : 'N/A',
            $checkInDate ? $checkInDate->format('M d, Y') : 'Not set',
            $checkOutDate ? $checkOutDate->format('M d, Y') : 'Not set',
            $stayDuration,
            $accommodation->airport_pickup_required ? 'Yes' : 'No',
            $accommodation->special_requirements ?? 'None',
            $accommodation->created_by_admin ? 'Admin' : 'Self-filled',
            $accommodation->created_at ? $accommodation->created_at->format('M d, Y h:i A') : 'N/A',
            $accommodation->updated_at ? $accommodation->updated_at->format('M d, Y h:i A') : 'N/A',
        ];
    }

    public function title(): string
    {
        return 'International Accommodations';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true, 'size' => 12]],
            
            // Add background color to header row
            'A1:U1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ]
            ],
        ];
    }
}
