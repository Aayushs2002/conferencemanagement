<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ConferenceRegistrationExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $registrants;
    public function __construct($registrants)
    {
        $this->registrants = $registrants;
    }
    public function collection()
    {
        // Separate dummy and real registrants
        $dummyRegistrants = $this->registrants->whereNull('user_id');
        $realRegistrants = $this->registrants->whereNotNull('user_id');

        // Sort real registrants alphabetically by user's full name
        $realRegistrants = $realRegistrants->sortBy(function ($registrant) {
            return strtolower($registrant->user->f_name . ' ' . $registrant->user->l_name);
        });

        // Merge: real registrants first (alphabetically), then dummy registrants
        $sortedRegistrants = $realRegistrants->merge($dummyRegistrants);

        $arrayData = [];
        foreach ($sortedRegistrants as $key => $registrant) {
            $arrayData[] = [
                'S.No.' => $key + 1,
                'Name' => $registrant->user?->fullName($registrant->user),
                'Email' => $registrant->user?->email ?? '-',
                'Phone' => $registrant->user?->userDetail->phone ?? '-',
                'councilNumber' => $registrant->user?->userDetail->council_number ?? '-',
                'totalAttendee' => $registrant->total_attendee,
                'country' => $registrant->user?->userDetail->country->country_name ?? '-',
            ];
        }
        return collect($arrayData);
    }

    public function headings(): array
    {
        return ["S.No.", "Name", "Email", "Phone", "Medical Council Number", "No. of People", "Country"];
    }
}
