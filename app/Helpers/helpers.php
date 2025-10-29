<?php

use App\Models\Cms\Blog;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\ScientificSessionCategory;
use App\Models\Payment\InternationalPayment;
use App\Models\User\ActivityLog;
use App\Models\User\Society;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

foreach (glob(__DIR__ . '/*_helpers.php') as $file) {
    require_once $file;
}


function getMetas($segment1, $segment2)
{

    if ($segment1 == 'blog') {
        $blog = Blog::where('slug', $segment2)->first();
        
        $imageUrl = null;
        if ($blog && $blog->image) {
            $imageUrl = url(Storage::url('blog/image/' . $blog->image));
        }
        
        $meta = (object) [
            'title' => $blog?->title,
            'description' => $blog?->description,
            'image' => $imageUrl,
        ];
        return $meta;
    } else {
        $meta = (object) [
            'title' => "Medcon Alert",
            'description' => "Medcon Alert - Manage your conferences with ease. Stay updated with the latest news, register for events, and connect with professionals in your field.",
            'image' =>  asset('frontend/assets/img/MEDCON-LOGO.png'), 
        ];
        return $meta;
    }
    // return null;
}

if (!function_exists('checkRegistration')) {
    function checkRegistration($conference)
    {
        // var_dump($conference);
        $conference_id =  Hashids::decode($conference);
        $checkRegistration = ConferenceRegistration::where(['conference_id' => $conference_id, 'user_id' => current_user()->id, 'status' => 1])->first();
        if ($checkRegistration) {
            return true;
        }
        return false;
    }
}
if (!function_exists('checkRegistrations')) {
    function checkRegistrations($conference)
    {
        // var_dump($conference);
        // $conference_id =  Hashids::decode($conference);
        $checkRegistration = ConferenceRegistration::where(['conference_id' => $conference->id, 'user_id' => current_user()->id, 'status' => 1])->first();
        if ($checkRegistration) {
            return true;
        }
        return false;
    }
}
if (!function_exists('getConference')) {
    function getConference($conference)
    {
        $conference_id =  Hashids::decode($conference);

        $conference = Conference::where('id', $conference_id)->first();

        if (!$conference) {
            return false;
        }
        return $conference;
    }
}
if (!function_exists('getSociety')) {
    function getSociety($society)
    {
        $society_id =  Hashids::decode($society);

        $society = Society::where('id', $society_id)->first();

        if (!$society) {
            return false;
        }
        return $society;
    }
}

if (!function_exists('getCategories')) {
    function getCategories($parent_id)
    {
        $conference_id =  Hashids::decode(request()->segment(4));

        $conference = Conference::where('id', $conference_id)->first();
        $categories = ScientificSessionCategory::where(function ($query) use ($conference, $parent_id) {
            $query->where('status', 1)
                ->where('parent_id', $parent_id)
                ->where(function ($q) use ($conference) {
                    $q->whereNull('conference_id')
                        ->orWhere('conference_id', $conference->id);
                });
        })->get();
        return $categories;
    }
}

if (!function_exists('logActivity')) {

    function logActivity($conference_id, $action, $description = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'conference_id' => $conference_id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}

if (!function_exists('parseTemplate')) {
    function parseTemplate($templateString, $data)
    {
        foreach ($data as $key => $value) {
            $templateString = str_replace('{' . $key . '}', $value, $templateString);
        }
        return $templateString;
    }
}

if (!function_exists('internationalPayment')) {
    function internationalPayment($society)
    {
        $society_id =  Hashids::decode($society);
        // dd($society_id);
        $internationalPayment = InternationalPayment::where(['society_id' => $society_id, 'status' => 1])->first();
        return $internationalPayment;
    }
}


if (!function_exists('numberToWord')) {
    function numberToWord($num = '')
    {
        $num    = (string) ((int) $num);

        if ((int) ($num) && ctype_digit($num)) {
            $words  = array();

            $num    = str_replace(array(',', ' '), '', trim($num));

            $list1  = array(
                '',
                'one',
                'two',
                'three',
                'four',
                'five',
                'six',
                'seven',
                'eight',
                'nine',
                'ten',
                'eleven',
                'twelve',
                'thirteen',
                'fourteen',
                'fifteen',
                'sixteen',
                'seventeen',
                'eighteen',
                'nineteen'
            );

            $list2  = array(
                '',
                'ten',
                'twenty',
                'thirty',
                'forty',
                'fifty',
                'sixty',
                'seventy',
                'eighty',
                'ninety',
                'hundred'
            );

            $list3  = array(
                '',
                'thousand',
                'million',
                'billion',
                'trillion',
                'quadrillion',
                'quintillion',
                'sextillion',
                'septillion',
                'octillion',
                'nonillion',
                'decillion',
                'undecillion',
                'duodecillion',
                'tredecillion',
                'quattuordecillion',
                'quindecillion',
                'sexdecillion',
                'septendecillion',
                'octodecillion',
                'novemdecillion',
                'vigintillion'
            );

            $num_length = strlen($num);
            $levels = (int) (($num_length + 2) / 3);
            $max_length = $levels * 3;
            $num    = substr('00' . $num, -$max_length);
            $num_levels = str_split($num, 3);

            foreach ($num_levels as $num_part) {
                $levels--;
                $hundreds   = (int) ($num_part / 100);
                $hundreds   = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ($hundreds == 1 ? '' : 's') . ' ' : '');
                $tens       = (int) ($num_part % 100);
                $singles    = '';

                if ($tens < 20) {
                    $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                } else {
                    $tens = (int) ($tens / 10);
                    $tens = ' ' . $list2[$tens] . ' ';
                    $singles = (int) ($num_part % 10);
                    $singles = ' ' . $list1[$singles] . ' ';
                }
                $words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_part)) ? ' ' . $list3[$levels] . ' ' : '');
            }
            $commas = count($words);
            if ($commas > 1) {
                $commas = $commas - 1;
            }

            $words  = implode(', ', $words);

            $words  = trim(str_replace(' ,', ',', ucwords($words)), ', ');
            if ($commas) {
                $words  = str_replace(',', ' and', $words);
            }

            return $words;
        } else if (!((int) $num)) {
            return 'Zero';
        }
        return '';
    }
}


if (!function_exists('super_generate_breadcrumbs')) {
    function super_generate_breadcrumbs($society = null, $conference = null)
    {

        $routeName = request()->route()->getName();
        // dd($routeName);
        $breadcrumbs = ['Dashboard' =>  route('dashboard')];

        // dd($routeName);
        switch ($routeName) {
            case 'society.index':
                $breadcrumbs['Society'] = route('society.index');
                break;
            case 'society.create':
                $breadcrumbs['Society'] = route('society.index');
                $breadcrumbs['Create'] = '#';
                break;
            case 'society.edit':
                $breadcrumbs['Society'] = route('society.index');
                $breadcrumbs['Edit'] = '#';
                break;
            case 'name-prefix.index':
                $breadcrumbs['Name Prefix'] = route('name-prefix.index');
                break;
            case 'name-prefix.create':
                $breadcrumbs['Name Prefix'] = route('name-prefix.index');
                $breadcrumbs['Create'] = '#';
                break;
            case 'name-prefix.edit':
                $breadcrumbs['Name Prefix'] = route('name-prefix.index');
                $breadcrumbs['Edit'] = '#';
                break;
            case 'institution.index':
                $breadcrumbs['Institution'] = route('institution.index');
                break;
            case 'institution.create':
                $breadcrumbs['Institution'] = route('institution.index');
                $breadcrumbs['Create'] = '#';
                break;
            case 'institution.edit':
                $breadcrumbs['Institution'] = route('institution.index');
                $breadcrumbs['Edit'] = '#';
                break;
            case 'designation.index':
                $breadcrumbs['Designation'] = route('designation.index');
                break;
            case 'designation.create':
                $breadcrumbs['Designation'] = route('designation.index');
                $breadcrumbs['Create'] = '#';
                break;
            case 'designation.edit':
                $breadcrumbs['Designation'] = route('designation.index');
                $breadcrumbs['Edit'] = '#';
                break;
            case 'department.index':
                $breadcrumbs['Department'] = route('department.index');
                break;
            case 'department.create':
                $breadcrumbs['Department'] = route('department.index');
                $breadcrumbs['Create'] = '#';
                break;
            case 'department.edit':
                $breadcrumbs['Department'] = route('department.index');
                $breadcrumbs['Edit'] = '#';
                break;
            case 'permission.index':
                $breadcrumbs['Permission'] = route('permission.index');
                break;
            case 'permission.create':
                $breadcrumbs['Permission'] = route('permission.index');
                $breadcrumbs['Create'] = '#';
                break;
            case 'permission.edit':
                $breadcrumbs['Permission'] = route('permission.index');
                $breadcrumbs['Edit'] = '#';
                break;
            case 'user.index':
                $breadcrumbs['User'] = route('user.index');
                break;
            case 'security.index':
                $breadcrumbs['Security'] = '#';
                break;
        }

        return $breadcrumbs;
    }
}
if (!function_exists('society_generate_breadcrumbs')) {
    function society_generate_breadcrumbs($society = null, $conference = null)
    {

        $routeName = request()->route()->getName();
        // dd($routeName);
        $breadcrumbs = ['Dashboard' =>  route('society.dashboard', $society)];

        switch ($routeName) {
            case 'conference.index':
                $breadcrumbs['Conference'] = route('conference.index', $society);
                break;
            case 'my-society.conference':
                $breadcrumbs['Conference'] = route('my-society.conference', $society);
                break;
            case 'conference.create':
                $breadcrumbs['Conference'] = route('conference.index', $society);
                $breadcrumbs['Create'] = '#';
                break;
            case 'conference.edit':
                $breadcrumbs['Conference'] = route('conference.index', $society);
                $breadcrumbs['Edit'] = '#';
                break;
            case 'memberType.index':
                $breadcrumbs['Member Type'] = route('memberType.index', $society);
                break;
            case 'memberType.create':
                $breadcrumbs['Member Type'] = route('memberType.index', $society);
                $breadcrumbs['Create'] = '#';
                break;
            case 'memberType.edit':
                $breadcrumbs['Member Type'] = route('memberType.index', $society);
                $breadcrumbs['Edit'] = '#';
                break;
            case 'payment.setting':
                $breadcrumbs['Payment Setting'] = route('payment.setting', $society);
                break;
            case 'security.index.society':
                $breadcrumbs['Security'] = '#';
                break;
        }

        return $breadcrumbs;
    }
}

if (!function_exists('conference_generate_breadcrumbs')) {
    function conference_generate_breadcrumbs($society = null, $conference = null)
    {

        $routeName = request()->route()->getName();
        // dd($routeName);
        $breadcrumbs = ['Dashboard' =>  route('conference.openConferencePortal', [$society, $conference])];

        // dd($routeName);
        switch ($routeName) {
            case 'my-society.conference.create':
                $breadcrumbs['Conference Registration'] = route('my-society.conference.create', [$society, $conference]);
                break;
            case 'conference.conference-registration.index':
                $breadcrumbs['Registrant'] = route('conference.conference-registration.index', [$society, $conference]);
                break;
            case 'conference.conference-registration.registrationOrInvitation':
                $breadcrumbs['Registration/Invitation'] = route('conference.conference-registration.registrationOrInvitation', [$society, $conference]);
                // $breadcrumbs['Create'] = '#';
                break;
            case 'conference.conference-registration.registerForExceptionalCase':
                $breadcrumbs['Exception Case'] = route('conference.conference-registration.registerForExceptionalCase', [$society, $conference]);
                // $breadcrumbs['Create'] = '#';
                break;
            case 'pass-setting.index':
                $breadcrumbs['Pass Setting'] = route('pass-setting.index', [$society, $conference]);
                break;
            case 'pass-setting.create':
                $breadcrumbs['Pass Setting'] = route('pass-setting.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'pass-setting.edit':
                $breadcrumbs['Pass Setting'] = route('pass-setting.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;
            case 'conference-certificate.index':
                $breadcrumbs['Certificate Setting'] = route('conference-certificate.index', [$society, $conference]);
                break;
            case 'conference-certificate.create':
                $breadcrumbs['Pass Setting'] = route('conference-certificate.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'conference-certificate.edit':
                $breadcrumbs['Pass Setting'] = route('conference-certificate.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;
            case 'my-society.conference.submission.index':
                $breadcrumbs['Submission'] = route('my-society.conference.submission.index', [$society, $conference]);
                break;
            case 'my-society.conference.submission.create':
                $breadcrumbs['Submission'] = route('my-society.conference.submission.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'my-society.conference.submission.edit':
                $breadcrumbs['Submission'] = route('my-society.conference.submission.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;
            case 'submission.index':
                $breadcrumbs['Submission'] = route('submission.index', [$society, $conference]);
                break;
            case 'submission.author.index':
                $breadcrumbs['Submission'] = route('submission.index', [$society, $conference]);
                $breadcrumbs['Author'] = '#';
                break;
            case 'submission.viewDiscussion':
                $breadcrumbs['Submission'] = route('submission.index', [$society, $conference]);
                $breadcrumbs['Discussion'] = '#';
                break;

            case 'submission.setting':
                $breadcrumbs['Submission Setting'] = route('submission.setting', [$society, $conference]);
                // $breadcrumbs['Edit'] = '#';
                break;

            case 'submission.category-majortrack.index':
                $breadcrumbs['Category/Major Track'] = route('submission.category-majortrack.index', [$society, $conference]);
                break;
            case 'submission.category-majortrack.create':
                $breadcrumbs['Category/Major Track'] = route('submission.category-majortrack.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'submission.category-majortrack.edit':
                $breadcrumbs['Category/Major Track'] = route('submission.category-majortrack.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'scientific-session.index':
                $breadcrumbs['Scientific Session'] = route('scientific-session.index', [$society, $conference]);
                break;
            case 'scientific-session.create':
                $breadcrumbs['Scientific Session'] = route('scientific-session.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'scientific-session.edit':
                $breadcrumbs['Scientific Session'] = route('scientific-session.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'category.index':
                $breadcrumbs['Scientific Session Category'] = route('category.index', [$society, $conference]);
                break;
            case 'category.create':
                $breadcrumbs['Scientific Session Category'] = route('category.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'category.edit':
                $breadcrumbs['Scientific Session Category'] = route('category.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'hall.index':
                $breadcrumbs['Scientific Session Hall'] = route('hall.index', [$society, $conference]);
                break;
            case 'hall.create':
                $breadcrumbs['Scientific Session Hall'] = route('hall.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'hall.edit':
                $breadcrumbs['Scientific Session Hall'] = route('hall.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;
            case 'my-society.conference.workshop.index':
                $breadcrumbs['Workshop Registration'] = route('my-society.conference.workshop.index', [$society, $conference]);
                break;

            case 'workshop.index':
                $breadcrumbs['Workshop'] = route('workshop.index', [$society, $conference]);
                break;
            case 'workshop.create':
                $breadcrumbs['Workshop'] = route('workshop.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'workshop.edit':
                $breadcrumbs['Workshop'] = route('workshop.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'workshop.workshop-trainer.index':
                $breadcrumbs['Workshop'] = route('workshop.index', [$society, $conference]);
                $breadcrumbs['Trainer'] = '#';
                break;
            case 'workshop.workshop-trainer.create':
                $breadcrumbs['Workshop'] = route('workshop.index', [$society, $conference]);
                $breadcrumbs['Trainer'] = route('workshop.workshop-trainer.index', [$society, $conference, request()->segment(7)]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'workshop.workshop-trainer.edit':
                $breadcrumbs['Workshop'] = route('workshop.index', [$society, $conference]);
                $breadcrumbs['Trainer'] = route('workshop.workshop-trainer.index', [$society, $conference, request()->segment(8)]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'workshop.workshop-registration.registerForNewUser':
                $breadcrumbs['Workshop Register New User'] = '#';
                break;
            case 'workshop.workshop-registration.registerForExceptionalCase':
                $breadcrumbs['Workshop Exceptional Case'] = '#';
                break;

            case 'workshop-pass-settings.index':
                $breadcrumbs['Workshop Pass Setting'] = route('workshop-pass-settings.index', [$society, $conference]);
                break;
            case 'workshop-pass-settings.create':
                $breadcrumbs['Workshop Pass Setting'] = route('workshop-pass-settings.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'workshop-pass-settings.edit':
                $breadcrumbs['Workshop Pass Setting'] = route('workshop-pass-settings.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'committee.index':
                $breadcrumbs['Committee'] = route('committee.index', [$society, $conference]);
                break;
            case 'committee.create':
                $breadcrumbs['Committee'] = route('committee.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'committee.edit':
                $breadcrumbs['Committee'] = route('committee.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'committeeMember.index':
                $breadcrumbs['Committee'] = route('committee.index', [$society, $conference]);
                $breadcrumbs['Committee Member'] = route('committeeMember.index', [$society, $conference, request()->segment(8)]);
                break;
            case 'committeeMember.create':
                $breadcrumbs['Committee'] = route('committee.index', [$society, $conference]);
                $breadcrumbs['Committee Member'] = route('committeeMember.index', [$society, $conference, request()->segment(8)]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'committeeMember.edit':
                $breadcrumbs['Committee'] = route('committee.index', [$society, $conference]);
                // $breadcrumbs['Committee Member'] = route('committeeMember.index', [$society, $conference, 'past-president-1754391809']);
                $breadcrumbs['Committee Member'] = '#';
                $breadcrumbs['Edit'] = '#';
                break;

            case 'committe-designation.index':
                $breadcrumbs['Committee Designation'] = route('committe-designation.index', [$society, $conference]);
                break;
            case 'committe-designation.create':
                $breadcrumbs['Committee Designation'] = route('committe-designation.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'committe-designation.edit':
                $breadcrumbs['Committee Designation'] = route('committe-designation.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'sponsor.index':
                $breadcrumbs['Sponsor'] = route('sponsor.index', [$society, $conference]);
                break;
            case 'sponsor.create':
                $breadcrumbs['Sponsor'] = route('sponsor.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'sponsor.edit':
                $breadcrumbs['Sponsor'] = route('sponsor.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'sponsor-category.index':
                $breadcrumbs['Sponsor Category'] = route('sponsor-category.index', [$society, $conference]);
                break;
            case 'sponsor-category.create':
                $breadcrumbs['Sponsor Category'] = route('sponsor-category.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'sponsor-category.edit':
                $breadcrumbs['Sponsor Category'] = route('sponsor-category.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'faq.index':
                $breadcrumbs['Faq'] = route('faq.index', [$society, $conference]);
                break;
            case 'faq.create':
                $breadcrumbs['Faq'] = route('faq.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'faq.edit':
                $breadcrumbs['Faq'] = route('faq.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'faq-category.index':
                $breadcrumbs['Faq Category'] = route('faq-category.index', [$society, $conference]);
                break;
            case 'faq-category.create':
                $breadcrumbs['Faq Category'] = route('faq-category.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'faq-category.edit':
                $breadcrumbs['Faq Category'] = route('faq-category.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;
            case 'signup-user.index':
                $breadcrumbs['Signed Up Users'] = route('signup-user.index', [$society, $conference]);
                break;
            case 'roles.index':
                $breadcrumbs['Roles'] = route('roles.index', [$society, $conference]);
                break;

            case 'download.index':
                $breadcrumbs['Download'] = route('download.index', [$society, $conference]);
                break;
            case 'download.create':
                $breadcrumbs['Download'] = route('download.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'download.edit':
                $breadcrumbs['Download'] = route('download.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'notice.index':
                $breadcrumbs['News/Notice'] = route('notice.index', [$society, $conference]);
                break;
            case 'notice.create':
                $breadcrumbs['News/Notice'] = route('notice.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'notice.edit':
                $breadcrumbs['News/Notice'] = route('notice.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'hotel.index':
                $breadcrumbs['Accomodation'] = route('hotel.index', [$society, $conference]);
                break;
            case 'hotel.create':
                $breadcrumbs['Accomodation'] = route('hotel.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'hotel.edit':
                $breadcrumbs['Accomodation'] = route('hotel.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;

            case 'activity-log.index':
                $breadcrumbs['Log Report'] = route('activity-log.index', [$society, $conference]);
                break;
            case 'security.index.full':
                $breadcrumbs['Security'] = '#';
                break;
            case 'email-template.index':
                $breadcrumbs['Email Template'] = '#';
                break;
            case 'email-template.create':
                $breadcrumbs['Email Template'] = route('email-template.index', [$society, $conference]);
                $breadcrumbs['Create'] = '#';
                break;
            case 'email-template.edit':
                $breadcrumbs['Email Template'] = route('email-template.index', [$society, $conference]);
                $breadcrumbs['Edit'] = '#';
                break;
        }

        return $breadcrumbs;
    }
}
