<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceStatusExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $allData;

    public function __construct($allData)
    {
        $this->allData = $allData;
    }

    public function collection()
    {
        $arrayData = [];
        
        foreach ($this->allData as $key => $item) {
            // Get name based on record type
            if ($item->record_type === 'registrant') {
                $middleName = !empty($item->m_name) ? $item->m_name . ' ' : '';
                $name = $item->f_name . ' ' . $middleName . $item->l_name;
                $type = 'Registrant';
            } else {
                $name = $item->sponsor_name;
                $type = 'Sponsor';
                if (!empty($item->contact_person)) {
                    $name .= ' (Contact: ' . $item->contact_person . ')';
                }
            }
            
            // Get accompanying persons
            $accompanyPersonsNames = [];
            if ($item->total_attendee > 1 && isset($item->accompanyPersons) && count($item->accompanyPersons) > 0) {
                foreach ($item->accompanyPersons as $accompanyPerson) {
                    $accompanyPersonsNames[] = $accompanyPerson->person_name;
                }
            }
            $accompanyPersonsList = !empty($accompanyPersonsNames) ? implode(', ', $accompanyPersonsNames) : ($item->record_type === 'sponsor' ? 'N/A' : 'None');
            
            // Get attendance dates
            $attendanceDates = [];
            if (isset($item->attendences)) {
                foreach ($item->attendences as $attendance) {
                    $attendanceDates[] = \Carbon\Carbon::parse($attendance->created_at)->format('d M, h:i A');
                }
            }
            $attendanceList = !empty($attendanceDates) ? implode('; ', $attendanceDates) : 'Not marked';
            
            // Get meal data
            $mealData = [];
            if (isset($item->meals)) {
                foreach ($item->meals as $meal) {
                    $date = \Carbon\Carbon::parse($meal->created_at)->format('d M, h:i A');
                    $lunch = $meal->lunch_taken > 0 ? "Taken ({$meal->lunch_taken})" : 'Not Taken';
                    $dinner = $meal->dinner_taken > 0 ? "Taken ({$meal->dinner_taken})" : 'Not Taken';
                    $mealData[] = "$date - Lunch: $lunch, Dinner: $dinner";
                }
            }
            $mealList = !empty($mealData) ? implode('; ', $mealData) : 'No meals';
            
            // Kit status (only for registrants)
            if ($item->record_type === 'registrant') {
                $kitStatus = isset($item->conferenceRegistrationKit) && $item->conferenceRegistrationKit ? 'Taken' : 'Not Taken';
            } else {
                $kitStatus = 'N/A (Sponsor)';
            }
            
            $arrayData[] = [
                'S.No.' => $key + 1,
                'Type' => $type,
                'Registration ID' => $item->registration_id ?? '-',
                'Name' => $name,
                'Email' => $item->email ?? '-',
                'Phone' => $item->phone ?? '-',
                'Country' => $item->country_name ?? '-',
                'Institution' => $item->record_type === 'registrant' ? ($item->institution_name ?? '-') : ($item->category_name ?? '-'),
                'Total Attendees' => $item->total_attendee,
                'Accompanying Persons' => $accompanyPersonsList,
                'Attendance Count' => isset($item->attendences) ? count($item->attendences) : 0,
                'Attendance Details' => $attendanceList,
                'Total Lunch' => isset($item->total_lunch_consumed) ? $item->total_lunch_consumed : 0,
                'Total Dinner' => isset($item->total_dinner_consumed) ? $item->total_dinner_consumed : 0,
                'Meal Details' => $mealList,
                'Kit Status' => $kitStatus,
            ];
        }
        
        return collect($arrayData);
    }

    public function headings(): array
    {
        return [
            'S.No.',
            'Type',
            'Registration ID',
            'Name',
            'Email',
            'Phone',
            'Country',
            'Institution/Category',
            'Total Attendees',
            'Accompanying Persons',
            'Attendance Count',
            'Attendance Details',
            'Total Lunch',
            'Total Dinner',
            'Meal Details',
            'Kit Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
