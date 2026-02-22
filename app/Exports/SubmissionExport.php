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
    protected $includeExpertInfo;

    public function __construct($submissions, $includeExpertInfo = false)
    {
        $this->submissions = $submissions;
        $this->includeExpertInfo = $includeExpertInfo;
    }

    /**
     * Calculate total score based on rating type
     */
    protected function calculateTotalScore($submission)
    {
        if (!$submission->submissionRating) {
            return 'N/A';
        }

        $rating = $submission->submissionRating;
        
        // Check if section-based ratings exist
        $articleTypeSections = null;
        if ($submission->articleType && $submission->articleType->setting) {
            $articleTypeSections = $submission->articleType->setting->sections ?? null;
        }

        if (!empty($articleTypeSections) && is_array($articleTypeSections) && !empty($rating->section_ratings)) {
            // Section-based ratings
            $total = collect($rating->section_ratings)->sum('rating') +
                     ($rating->title_rating ?? 0) +
                     ($rating->grammar ?? 0) +
                     ($rating->overall_rating ?? 0);
            return $total;
        } elseif ($rating->overall_rating !== null && 
                  $rating->introduction === null && 
                  $rating->method === null && 
                  $rating->result === null && 
                  $rating->conclusion === null) {
            // Overall rating only (structure checkbox)
            return $rating->overall_rating;
        } else {
            // Default ratings (Introduction, Method, Result, Conclusion, Grammar)
            $total = ($rating->introduction ?? 0) +
                     ($rating->method ?? 0) +
                     ($rating->result ?? 0) +
                     ($rating->conclusion ?? 0) +
                     ($rating->grammar ?? 0);
            return $total;
        }
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
            
            // Get individual scores
            $rating = $submission->submissionRating;
            $introduction = 'N/A';
            $method = 'N/A';
            $result = 'N/A';
            $conclusion = 'N/A';
            $grammar = 'N/A';
            $titleRating = 'N/A';
            $overallRating = 'N/A';
            $sectionRatings = 'N/A';

            if ($rating) {
                $introduction = $rating->introduction ?? 'N/A';
                $method = $rating->method ?? 'N/A';
                $result = $rating->result ?? 'N/A';
                $conclusion = $rating->conclusion ?? 'N/A';
                $grammar = $rating->grammar ?? 'N/A';
                $titleRating = $rating->title_rating ?? 'N/A';
                $overallRating = $rating->overall_rating ?? 'N/A';
                
                // Format section ratings if present
                if (!empty($rating->section_ratings) && is_array($rating->section_ratings)) {
                    $sectionParts = [];
                    foreach ($rating->section_ratings as $section) {
                        $sectionName = $section['name'] ?? 'Section';
                        $sectionScore = $section['rating'] ?? 'N/A';
                        $sectionParts[] = "{$sectionName}: {$sectionScore}";
                    }
                    $sectionRatings = implode(' | ', $sectionParts);
                }
            }
            
            $rowData = [
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
                'Introduction' => $introduction,
                'Method' => $method,
                'Result' => $result,
                'Conclusion' => $conclusion,
                'Grammar' => $grammar,
                'Title Rating' => $titleRating,
                'Section Ratings' => $sectionRatings,
                'Overall Rating' => $overallRating,
                'Total Score' => $this->calculateTotalScore($submission),
            ];

            // Add expert information if filter is active
            if ($this->includeExpertInfo) {
                $expertName = 'Not Assigned';
                $reviewDeadline = 'N/A';
                
                if ($submission->expert_id && $submission->expert) {
                    $expertName = $submission->expert->fullName($submission->expert);
                }
                
                if ($submission->review_deadline) {
                    $reviewDeadline = \Carbon\Carbon::parse($submission->review_deadline)->format('Y-m-d');
                }
                
                $rowData['Expert Assigned'] = $expertName;
                $rowData['Review Deadline'] = $reviewDeadline;
            }

            $arrayData[] = $rowData;
        }
        
        return collect($arrayData);
    }

    public function headings(): array
    {
        $headings = [
            'Author Name',
            'Affiliation - Designation, Institution, Address',
            'Email',
            'Phone',
            'Title',
            'Presentation Type',
            'Presentation Category',
            'Theme/Sub Theme',
            'Major Track',
            'Status',
            'Introduction',
            'Method',
            'Result',
            'Conclusion',
            'Grammar',
            'Title Rating',
            'Section Ratings',
            'Overall Rating',
            'Total Score'
        ];

        // Add expert columns if filter is active
        if ($this->includeExpertInfo) {
            $headings[] = 'Expert Assigned';
            $headings[] = 'Review Deadline';
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
