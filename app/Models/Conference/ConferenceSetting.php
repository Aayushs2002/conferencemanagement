<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceSetting extends Model
{
    protected $fillable = [
        'conference_id',
        'name',
        'signature',
        'registration_guideline',
        'registration_guideline_youtube',
        'submission_guideline_youtube',
        'expert_guideline_youtube',
        'logo_display_type',
        'payment_instruction',
        'terms_conditions',
        'privacy_policy',
        'speaker_registration_required',
        'registration_open_date',
        'workshop_registration_open_date',
        'workshop_application_deadline',
        'cpd_points_required',
        'show_stats_dashboard',
        'conference_registration_verification_for_all',
        'addon_availability',
        'gala_dinner_enabled',
        'submission_cc_emails',
        'reviewer_assignment_cc_emails',
        'conference_registration_cc_emails',
        'workshop_registration_cc_emails',
        'closing_message',
        'portal_access_end_at',
        'payment_voucher_header_color',
        'committee_static_page_enabled',
        'committee_static_page_content',
    ];
}
 