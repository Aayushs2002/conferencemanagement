<?php

use App\Http\Controllers\Backend\Committee\CommitteeController;
use App\Http\Controllers\Backend\Committee\CommitteeDesignationController;
use App\Http\Controllers\Backend\Committee\CommitteeMemberController;
use App\Http\Controllers\Backend\Conference\ConferenceCertificateController;
use App\Http\Controllers\Backend\Conference\ConferenceController;
use App\Http\Controllers\backend\Conference\ConferenceMemberTypePriceController;
use App\Http\Controllers\Backend\Conference\ConferenceRegistrationController;
use App\Http\Controllers\Backend\Conference\PassSettingController;
use App\Http\Controllers\Backend\Dashboard\ConferenceDashboardController;
use App\Http\Controllers\Backend\Download\DownloadController;
use App\Http\Controllers\Backend\Faq\FaqCategoryController;
use App\Http\Controllers\Backend\Faq\FaqController;
use App\Http\Controllers\Backend\Notice\NoticeController;
use App\Http\Controllers\Backend\ScientificSession\HallController;
use App\Http\Controllers\Backend\ScientificSession\PollController;
use App\Http\Controllers\Backend\ScientificSession\ScientificSessionCategoryController;
use App\Http\Controllers\Backend\ScientificSession\ScientificSessionController;
use App\Http\Controllers\Backend\Sponsor\SponsorCategoryController;
use App\Http\Controllers\Backend\Sponsor\SponsorController;
use App\Http\Controllers\Backend\Submission\AuthorController;
use App\Http\Controllers\Backend\Submission\SubmissionCategoryMajorTrackContoller;
use App\Http\Controllers\Backend\Submission\SubmissionController;
use App\Http\Controllers\Backend\Submission\SubmissionSettingController;
use App\Http\Controllers\Backend\User\SignupUserController;
use App\Http\Controllers\Backend\UserManagement\RoleController;
use App\Http\Controllers\Backend\Workshop\PassSetting\WorkshopPassSettingController;
use App\Http\Controllers\Backend\Workshop\Workshop\WorkshopController;
use App\Http\Controllers\Backend\Workshop\WorkshopRegistration\WorkshopRegistrationController;
use App\Http\Controllers\Backend\Workshop\WorkshopTrainer\WorkshopTrainerController;
use App\Models\Sponsor\Sponsor;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    //conference dashboard
    Route::controller(ConferenceDashboardController::class)->name('dashboard.')->group(function () {
        Route::get('/registration-data', 'registrationData')->name('registrationData');
    });

    //conference route started

    Route::prefix('/society/{society}')->group(function () {
        Route::resource('/conference', ConferenceController::class)->except('show', 'destroy');
    });
    Route::post('/conference/show', [ConferenceController::class, 'view'])->name('conference.show');

    //conference open portal route
    Route::prefix('/society/{society}/conference/{conference}')->group(function () {
        Route::get('/dashboard', [ConferenceController::class, 'openConferencePortal'])->name('conference.openConferencePortal');
        Route::get('/dashboard/attendance-status', [ConferenceController::class, 'viewAttendanceStatus'])->name('conference.viewAttendanceStatus');
    });
    Route::get('/conference/stats', [ConferenceController::class, 'getStats'])->name('conference.stats');

    //conference route ended
    //conference member type price route started
    Route::post('/conference/price-form', [ConferenceMemberTypePriceController::class, 'priceForm'])->name('conference.priceForm');
    Route::post('/conference/price-submit', [ConferenceMemberTypePriceController::class, 'priceSubmit'])->name('conference.priceSubmit');
    //conference member type price route ended


    Route::controller(ConferenceRegistrationController::class)->name('conference.conference-registration.')->middleware('auto.conf.permission')->prefix('/society/{society}/conference/{conference}/conference-registration')->group(function () {
        Route::get('/registrant', 'index')->name('index');
        Route::post('/view-data', 'show')->name('show');
        Route::get('/register-for-exceptional-case', 'registerForExceptionalCase')->name('registerForExceptionalCase');
        Route::post('/register-for-exceptional-case-submit', 'registerForExceptionalCaseSubmit')->name('registerForExceptionalCaseSubmit');
        Route::post('/add-person', 'addPerson')->name('addPerson');
        Route::post('/add-person-submit', 'addPersonSubmit')->name('addPersonSubmit');
        Route::post('/convert-registrant-type', 'convertRegistrantType')->name('convertRegistrantType');
        Route::post('/convert-registrant-type-submit', 'convertRegistrantTypeSubmit')->name('convertRegistrantTypesubmit');
        Route::get('/registration-or-invitation', 'registrationOrInvitation')->name('registrationOrInvitation');
        Route::post('/registration-or-invitation-submit', 'registrationOrInvitationSubmit')->name('registrationOrInvitationSubmit');
        Route::get('/exportExcel',  'excelExport')->name('excelExport');
        Route::get('/generate-individual-pass/{conferenceRegistration}', 'generateIndividualPass')->name('generateIndividualPass');
    });

    Route::controller(ConferenceRegistrationController::class)->name('conference.conference-registration.')->group(function () {
        Route::post('/participant/take-attendance', 'takeAttendance')->name('takeAttendance');
        Route::post('/participant/take-meal', 'takeMeal')->name('takeMeal');
        Route::post('/participant/take-conference-kit', 'takeConferenceKit')->name('takeConferenceKit');
    });

    Route::controller(PassSettingController::class)->middleware('auto.conf.permission')->prefix('/society/{society}/conference/{conference}/conference-registration')->group(function () {
        Route::resource('pass-setting', PassSettingController::class);
    });

    Route::prefix('/society/{society}/conference/{conference}/conference-registration')->middleware('auto.conf.permission')->group(function () {
        Route::resource('conference-certificate', ConferenceCertificateController::class);
    });

    Route::get('/conference-certificate/{conference-certificate}/signature/{signature}', [ConferenceCertificateController::class, 'deleteImage'])->name('conference-certificate.signature.remove');


    //submission setting route started
    Route::controller(SubmissionSettingController::class)->middleware('auto.conf.permission')->prefix('/society/{society}/conference/{conference}/submission')->name('submission.')->group(function () {
        Route::get('/submission-setting', 'index')->name('setting');
        Route::post('/setting-submit', 'store')->name('settingSubmit');
    });
    //submission setting route ended

    //submission category/major track route start
    Route::controller(SubmissionCategoryMajorTrackContoller::class)->middleware('auto.conf.permission')->prefix('/society/{society}/conference/{conference}/submission/submission-cateogry-majortrack')->name('submission.category-majortrack.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{submissionCategoryMajortrack}', 'edit')->name('edit');
        Route::patch('/update/{submissionCategoryMajortrack}', 'update')->name('update');
        Route::delete('/destroy/{submissionCategoryMajortrack}', 'destroy')->name('destroy');
    });


    //Submission Route Started
    Route::controller(SubmissionController::class)->middleware('auto.conf.permission')->prefix('/society/{society}/conference/{conference}/submission')->name('submission.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('show', 'show')->name('show');
        Route::post('/forward-to-expert-form', 'expertForwardForm')->name('expertForwardForm');
        Route::post('/forward-to-expert', 'expertForward')->name('expertForward');
        Route::post('/sent-to-author-form', 'sentToAuthorForm')->name('sentToAuthorForm');
        Route::post('/sentToAuthor', 'sentToAuthor')->name('sentToAuthor');
        Route::get('/{submission}/view-discussion', 'viewDiscussion')->name('viewDiscussion');
        Route::get('/convert-presentation-type-request/{id}', 'convertPresentationTypeRequest')->name('convertPresentationTypeRequest');
        Route::post('viewScore', 'viewScore')->name('viewScore');
        Route::post('send-mail', 'sendMail')->name('sendMail');
        Route::post('send-mail-submit', 'sendMailSubmit')->name('sendMailSubmit');
        Route::get('/get-users', 'getUsersByTypeAndPresentation')->name('get.users');
        Route::get('/export-word', 'exportWord')->name('export.word');
    });

    Route::prefix('/society/{society}/conference/{conference}')->group(function () {
        Route::controller(AuthorController::class)->middleware('auto.conf.permission')->prefix('/submission/{submission}')->name('submission.author.')->group(function () {
            Route::get('/author', 'index')->name('index');
        });
    });
    //Submission Route Ended

    //Scientific Session route started
    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('/scientific-session', ScientificSessionController::class)->except('show');
        Route::get('/schedule-session', [ScientificSessionController::class, 'scheduleSession'])->name('scheduleSession');
    });
    //Scientific Session route ended

    //Scientific Session Poll route started
    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::controller(PollController::class)->prefix('/scientific-session/poll')->name('poll.')->group(function () {
            Route::get('/{id}', 'index')->name('index');
            Route::get('/create/{id}', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
        });
    });

    //Scientific Session category route Started
    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('/scientific-session/category', ScientificSessionCategoryController::class)->except('show');
    });
    //Scientific Session category route  End

    //Hall route started
    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('/scientific-session/hall', HallController::class)->except('show');
    });
    //Hall route ended
    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::controller(SignupUserController::class)->prefix('/user')->name('signup-user.')->group(function () {
            Route::get('/signup-users', 'index')->name('index');
            Route::post('/make-expert', 'makeExpert')->name('makeExpert');
            Route::post('invite-for-conference', 'inviteForConference')->name('inviteForConference');
            Route::post('invite-for-conference-submit', 'inviteForConferenceSubmit')->name('inviteForConferenceSubmit');
            Route::post('pass-desgination', 'passDesgination')->name('passDesgination');
            Route::post('pass-desgination-submit', 'passDesginationSubmit')->name('passDesginationSubmit');
            Route::post('edit-profile', 'editProfile')->name('editProfile');
            Route::post('edit-profile-submit', 'editProfileSubmit')->name('editProfileSubmit');
            Route::post('view-detail', 'show')->name('show');
            Route::post('merge-user', 'mergeUser')->name('mergeUser');
            Route::post('merge-user-submit', 'mergeUserSubmit')->name('mergeUserSubmit');
            Route::post('reset-admin-password', 'resetPassword')->name('resetPassword');
        });
    });

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('/workshop', WorkshopController::class)->except('show');
        Route::controller(WorkshopController::class)->name('workshop.')->prefix('/workshop')->group(function () {
            Route::post('/view-data', 'show')->name('show');
            Route::post('/allocate-price-form', 'allocatePriceForm')->name('allocatePriceForm');
            Route::post('/allocate-price-submit', 'allocatePriceSubmit')->name('allocatePriceSubmit');
        });

        Route::controller(WorkshopRegistrationController::class)->name('workshop.workshop-registration.')->prefix('/workshop/workshop-registration')->group(function () {
            Route::get('/register-for-exceptional-case', 'registerForExceptionalCase')->name('registerForExceptionalCase');
            Route::post('/register-for-exceptional-case-submit', 'registerForExceptionalCaseSubmit')->name('registerForExceptionalCaseSubmit');
            Route::get('/register-for-new-user', 'registerForNewUser')->name('registerForNewUser');
            Route::post('/register-for-new-user-submit', 'registerForNewUserSubmit')->name('registerForNewUserSubmit');
            Route::get('/{workshop}', 'index')->name('index');
        });


        Route::controller(WorkshopTrainerController::class)->name('workshop.workshop-trainer.')->prefix('/workshop/workshop-trainer')->group(function () {
            Route::get('/{workshop}', 'index')->name('index');
            Route::get('{workshop}/create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{workshop}/{trainer}', 'edit')->name('edit');
            Route::any('update/{trainer}', 'update')->name('update');
            Route::delete('destroy/{trainer}', 'destroy')->name('destroy');
        });

        Route::resource('workshop/workshop-pass-settings', WorkshopPassSettingController::class)->middleware('auto.conf.permission');


        Route::resource('/committee/committe-designation', CommitteeDesignationController::class)->except('show');

        Route::resource('/committee', CommitteeController::class)->except('show');

        // committee member routes
        Route::controller(CommitteeMemberController::class)->prefix('/committee/committee-members')->name('committeeMember.')->group(function () {
            Route::get('/index/{slug}', 'index')->name('index');
            Route::get('/create/{slug}', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{committee_member}', 'edit')->name('edit');
            Route::match(['put', 'patch'], '/update/{committee_member}', 'update')->name('update');
            Route::delete('/destroy/{committee_member}', 'destroy')->name('destroy');
            Route::get('/change-featured/{committee_member}', 'changeFeatured')->name('changeFeatured');
        });
    });

    Route::get('workshop-registrant/generate-pass/{workshop}', [WorkshopRegistrationController::class, 'generatePass'])->name('workshop.generatePass');

    Route::controller(WorkshopRegistrationController::class)->name('workshop.workshop-registration.')->group(function () {
        Route::post('/workshop/take-attendance', 'takeAttendance')->name('takeAttendance');
    });

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('sponsor/sponsor-category', SponsorCategoryController::class)->except('show');
        Route::resource('/sponsor', SponsorController::class)->except('show');
        Route::get('generate-pass', [SponsorController::class, 'generatePass'])->name('sponsor.generaate-pass');
    });

    Route::controller(SponsorController::class)->name('sponsor.')->group(function () {
        Route::post('/take-attendance', 'takeAttendance')->name('takeAttendance');
        Route::post('/take-meal', 'takeMeal')->name('takeMeal');
    });


    Route::get('sponsor/change-status/{sponsor}', [SponsorController::class, 'changeStatus'])->middleware('auto.conf.permission')->name('sponsor.changeStatus');
    Route::post('/sponsor/add-participant', [SponsorController::class, 'addParticipant'])->middleware('auto.conf.permission')->name('sponsor.addParticipant');
    Route::post('/sponsor/add-participant-submit', [SponsorController::class, 'addParticipantSubmit'])->middleware('auto.conf.permission')->name('sponsor.addParticipantSubmit');

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('faq', FaqController::class)->except('show');
        Route::resource('faq/faq-category', FaqCategoryController::class)->except('show');
    });
    Route::get('faq/change-status/{faq}', [FaqCategoryController::class, 'changeStatus'])->middleware('auto.conf.permission')->name('faq.changeStatus');

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('download', DownloadController::class)->except('show');
    });
    Route::get('download/change-status/{download}', [DownloadController::class, 'changeStatus'])->middleware('auto.conf.permission')->name('download.changeStatus');

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('notice', NoticeController::class)->except('show');
    });
    Route::post('/notice/view-data', [NoticeController::class, 'show'])->name('notice.show');
    Route::get('/notice/change-featured/{notice}', [NoticeController::class, 'changeFeatured'])->middleware('auto.conf.permission')->name('notice.changeFeatured');

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::post('/assign-role-form', [RoleController::class, 'assignRoleForm'])->name('assignRoleForm');
        Route::post('/assign-role-form-submit', [RoleController::class, 'assignRoleFormSubmit'])->name('assignRoleFormSubmit');
    });
});
Route::get('/participant/profile/{token}', [ConferenceRegistrationController::class, 'participantProfile']);
Route::get('workshop/participant/profile/{token}', [WorkshopRegistrationController::class, 'participantProfile']);
Route::get('/sponsor/profile/{token}', [SponsorController::class, 'sponsorProfile']);
