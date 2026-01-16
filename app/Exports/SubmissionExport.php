<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubmissionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $submissions;

    public function __construct($submissions)
    {
        $this->submissions = $submissions;
    }

    public function collection()
    {
        $arrayData = [];
        
        foreach ($this->submissions as $key => $submission) {
            // Get presenter information
            $presenter = $submission->presenter;
            $authorName = $presenter ? $presenter->fullName($presenter) : 'N/A';
            
            // Get main author (first author) for detailed information
            $mainAuthor = $submission->authors->first();
            
            // Build affiliation string
            $affiliation = '';
            if ($mainAuthor) {
                $affiliationParts = [];
                if ($mainAuthor->designation) {
                    $affiliationParts[] = $mainAuthor->designation;
                }
                if ($mainAuthor->institution) {
                    $affiliationParts[] = $mainAuthor->institution;
                }
                if ($mainAuthor->institution_address) {
                    $affiliationParts[] = $mainAuthor->institution_address;
                }
                $affiliation = implode(', ', array_filter($affiliationParts));
            }
            
            // Email and Phone
            $email = $mainAuthor ? $mainAuthor->email : ($presenter ? $presenter->email : 'N/A');
            $phone = $mainAuthor ? $mainAuthor->phone : ($presenter && $presenter->userDetail ? $presenter->userDetail->phone : 'N/A');
            
            // Title
            $title = $submission->title ?? 'N/A';
            
            // Presentation Type
            $presentationType = '';
            if ($submission->presentation_type == 1) {
                $presentationType = 'Poster';
            } elseif ($submission->presentation_type == 2) {
                $presentationType = 'Oral';
            } else {
                $presentationType = 'N/A';
            }
            
            // Presentation Category
            $presentationCategory = $submission->articleType ? $submission->articleType->name : 'N/A';
            
            // Theme/Sub Theme (Major Track)
            $themeSub = $submission->submissionCategoryMajorTrack ? $submission->submissionCategoryMajorTrack->title : 'N/A';
            
            // Major Track (same as Theme/Sub Theme in this context)
            $majorTrack = $themeSub;
            
            // Status
            $status = '';
            switch ($submission->request_status) {
                case 0:
                    $status = 'Pending';
                    break;
                case 1:
                    $status = 'Accepted';
                    break;
                case 2:
                    $status = 'Correction';
                    break;
                case 3:
                    $status = 'Rejected';
                    break;
                default:
                    $status = 'N/A';
                    break;
            }
            
            $arrayData[] = [
                'Author Name' => $authorName,
                'Affiliation' => $affiliation ?: 'N/A',
                'Email' => $email,
                'Phone' => $phone,
                'Title' => $title,
                'Presentation Type' => $presentationType,
                'Presentation Category' => $presentationCategory,
                'Theme/Sub Theme' => $themeSub,
                'Major Track' => $majorTrack,
                'Status' => $status,
            ];
        }
        
        return collect($arrayData);
    }

    public function headings(): array
    {
        return [
            'Author Name',
            'Affiliation - Designation, Institution, Address',
            'Email',
            'Phone',
            'Title',
            'Presentation Type',
            'Presentation Category',
            'Theme/Sub Theme',
            'Major Track',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
