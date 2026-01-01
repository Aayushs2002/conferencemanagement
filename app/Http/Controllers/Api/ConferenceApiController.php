<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accomodation\Hotel;
use App\Models\Committee\CommitteeMember;
use App\Models\Conference\ArticleType;
use App\Models\Conference\Conference;
use App\Models\Conference\OfficialMessage;
use App\Models\Conference\Submission;
use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\Download\Download;
use App\Models\Notice\Notice;
use App\Models\Workshop\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConferenceApiController extends Controller
{
    /**
     * Format image/file URLs based on field type and folder structure
     */
    private function formatMediaUrls($item, $fieldMappings)
    {
        if (!$item) return $item;
        
        foreach ($fieldMappings as $field => $folder) {
            if (isset($item->$field) && !empty($item->$field)) {
                // Handle array fields (like images, partner_logos)
                if (is_array($item->$field)) {
                    $item->$field = array_map(function($path) use ($folder) {
                        // For JSON objects with fileName key
                        if (is_array($path) && isset($path['fileName'])) {
                            return $this->getStorageUrl($folder . $path['fileName']);
                        }
                        return $this->getStorageUrl($folder . $path);
                    }, $item->$field);
                } else {
                    $item->$field = $this->getStorageUrl($folder . $item->$field);
                }
            }
        }
        
        return $item;
    }

    /**
     * Get full URL using Storage::url() pattern
     */
    private function getStorageUrl($path)
    {
        if (empty($path)) return null;
        
        // If already a full URL, return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // Use Laravel Storage URL generation
        return Storage::url($path);
    }

    /**
     * Get conference details by slug
     */
    public function getConference($slug)
    {
        try {
            $conference = Conference::with([
                'society',
                'ConferenceVenueDetail',
                'ConferenceOrganizer',
                'submissionSetting',
                'conferenceCertificate',
                'conferenceSetting'
            ])
                ->where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            // Format media URLs
            $conference = $this->formatMediaUrls($conference, [
                'conference_logo' => 'conference/conference/logo/',
                'conference_banner' => 'conference/conference/banner/',
                'partner_logos' => 'conference/partner-logos/'
            ]);

            return response()->json([
                'success' => true,
                'data' => $conference
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching conference',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conference basic info (lightweight)
     */
    public function getConferenceBasicInfo($slug)
    {
        try {
            $conference = Conference::select([
                'id',
                'conference_name',
                'abbreviation',
                'conference_theme',
                'conference_logo',
                'conference_banner',
                'partner_logos',
                'start_date',
                'end_date',
                'slug',
                'primary_color',
                'secendary_color'
            ])
                ->where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            // Format media URLs
            $conference = $this->formatMediaUrls($conference, [
                'conference_logo' => 'conference/conference/logo/',
                'conference_banner' => 'conference/conference/banner/',
                'partner_logos' => 'conference/partner-logos/'
            ]);

            return response()->json([
                'success' => true,
                'data' => $conference
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching conference',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conference about/venue details
     */
    public function getAboutConference($slug)
    {
        try {
            $conference = Conference::with([
                'ConferenceVenueDetail',
                'ConferenceOrganizer'
            ])
                ->where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'conference_name' => $conference->conference_name,
                    'conference_description' => $conference->conference_description,
                    'conference_theme' => $conference->conference_theme,
                    'start_date' => $conference->start_date,
                    'end_date' => $conference->end_date,
                    'venue' => $conference->ConferenceVenueDetail,
                    'organizer' => $conference->ConferenceOrganizer
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching conference details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get accommodation/hotels for conference
     */
    public function getAccommodation($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $hotels = Hotel::where('conference_id', $conference->id)
                ->where('status', 1)
                ->orderBy('display_order', 'asc')
                ->get();

            // Format media URLs for each hotel
            $hotels = $hotels->map(function($hotel) {
                return $this->formatMediaUrls($hotel, [
                    'featured_image' => 'hotel/featured-image/',
                    'cover_image' => 'hotel/cover-image/',
                    'images' => 'hotel/images/'
                ]);
            });

            return response()->json([
                'success' => true,
                'data' => $hotels
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching accommodation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workshops for conference
     */
    public function getWorkshops($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $workshops = Workshop::with([
                'workshopVenueDetail',
                'workshopTrainers',
                'workshopChairPersonDetail'
            ])
                ->where('conference_id', $conference->id)
                ->where('status', 1)
                ->get();

            // Format media URLs for each workshop
            $workshops = $workshops->map(function($workshop) {
                return $this->formatMediaUrls($workshop, [
                    'banner' => 'workshop/workshop/banner/',
                    'image' => 'workshop/workshop/image/',
                    'featured_image' => 'workshop/workshop/featured-image/'
                ]);
            });

            return response()->json([
                'success' => true,
                'data' => $workshops
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching workshops',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get news and notices for conference
     */
    public function getNewsNotices($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $notices = Notice::where('conference_id', $conference->id)
                ->where('status', 1)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            // Format media URLs for each notice
            $notices = $notices->map(function($notice) {
                return $this->formatMediaUrls($notice, [
                    'image' => 'notice/image/',
                    'attachment' => 'notice/attachment/'
                ]);
            });

            return response()->json([
                'success' => true,
                'data' => $notices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching news and notices',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get downloads for conference
     */
    public function getDownloads($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $downloads = Download::where('conference_id', $conference->id)
                ->where('status', 1)
                ->orderBy('is_featured', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            // Format media URLs for each download
            $downloads = $downloads->map(function($download) {
                return $this->formatMediaUrls($download, [
                    'file' => 'download/file/',
                    'image' => 'download/image/',
                    'thumbnail' => 'download/thumbnail/'
                ]);
            });

            return response()->json([
                'success' => true,
                'data' => $downloads
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching downloads',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get scientific sessions/submission tracks
     */
    public function getScientificSessions($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $tracks = SubmissionCategoryMajorTrack::where('conference_id', $conference->id)
                ->where('status', 1)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tracks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching scientific sessions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get article types for conference
     */
    public function getArticleTypes($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $articleTypes = ArticleType::where('conference_id', $conference->id)
                ->where('status', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $articleTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching article types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get committee members
     */
    public function getCommitteeMembers($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $committeeMembers = CommitteeMember::with(['user', 'committeeType'])
                ->where('conference_id', $conference->id)
                ->where('status', 1)
                ->orderBy('display_order', 'asc')
                ->get();

            // Format media URLs for each committee member
            $committeeMembers = $committeeMembers->map(function($member) {
                return $this->formatMediaUrls($member, [
                    'image' => 'committee/image/',
                    'photo' => 'committee/photo/'
                ]);
            });

            return response()->json([
                'success' => true,
                'data' => $committeeMembers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching committee members',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get official messages
     */
    public function getOfficialMessages($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $messages = OfficialMessage::where('conference_id', $conference->id)
                ->where('status', 1)
                ->orderBy('display_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Format media URLs for each message
            $messages = $messages->map(function($message) {
                return $this->formatMediaUrls($message, [
                    'image' => 'offical-message/image/',
                    'photo' => 'offical-message/photo/',
                    'attachment' => 'offical-message/attachment/'
                ]);
            });

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching official messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conference settings
     */
    public function getConferenceSettings($slug)
    {
        try {
            $conference = Conference::with('conferenceSetting')
                ->where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $conference->conferenceSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching conference settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all conference data (complete)
     */
    public function getAllConferenceData($slug)
    {
        try {
            $conference = Conference::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            $conference->load([
                'society',
                'ConferenceVenueDetail',
                'ConferenceOrganizer',
                'submissionSetting',
                'conferenceCertificate',
                'conferenceSetting'
            ]);

            // Format conference media URLs
            $conference = $this->formatMediaUrls($conference, [
                'conference_logo' => 'conference/conference/logo/',
                'conference_banner' => 'conference/conference/banner/',
                'partner_logos' => 'conference/partner-logos/'
            ]);

            $data = [
                'conference' => $conference,
                'hotels' => $conference->hotels->map(function($hotel) {
                    return $this->formatMediaUrls($hotel, [
                        'featured_image' => 'hotel/featured-image/',
                        'cover_image' => 'hotel/cover-image/',
                        'images' => 'hotel/images/'
                    ]);
                }),
                'workshops' => $conference->workshops()->with([
                    'workshopVenueDetail',
                    'workshopTrainers',
                    'workshopChairPersonDetail'
                ])->get()->map(function($workshop) {
                    return $this->formatMediaUrls($workshop, [
                        'banner' => 'workshop/workshop/banner/',
                        'image' => 'workshop/workshop/image/',
                        'featured_image' => 'workshop/workshop/featured-image/'
                    ]);
                }),
                'notices' => Notice::where('conference_id', $conference->id)
                    ->where('status', 1)
                    ->orderBy('date', 'desc')
                    ->get()
                    ->map(function($notice) {
                        return $this->formatMediaUrls($notice, [
                            'image' => 'notice/image/',
                            'attachment' => 'notice/attachment/'
                        ]);
                    }),
                'downloads' => $conference->downloads->map(function($download) {
                    return $this->formatMediaUrls($download, [
                        'file' => 'download/file/',
                        'image' => 'download/image/',
                        'thumbnail' => 'download/thumbnail/'
                    ]);
                }),
                'submission_tracks' => SubmissionCategoryMajorTrack::where('conference_id', $conference->id)
                    ->where('status', 1)
                    ->get(),
                'article_types' => ArticleType::where('conference_id', $conference->id)
                    ->where('status', 1)
                    ->get(),
                'committee_members' => CommitteeMember::with(['user', 'committeeType'])
                    ->where('conference_id', $conference->id)
                    ->where('status', 1)
                    ->orderBy('display_order', 'asc')
                    ->get()
                    ->map(function($member) {
                        return $this->formatMediaUrls($member, [
                            'image' => 'committee/image/',
                            'photo' => 'committee/photo/'
                        ]);
                    }),
                'official_messages' => $conference->officialMessages->map(function($message) {
                    return $this->formatMediaUrls($message, [
                        'image' => 'offical-message/image/',
                        'photo' => 'offical-message/photo/',
                        'attachment' => 'offical-message/attachment/'
                    ]);
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching conference data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
