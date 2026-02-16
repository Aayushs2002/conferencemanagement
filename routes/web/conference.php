<?php

use App\Http\Controllers\Backend\Committee\CommitteeController;
use App\Http\Controllers\Backend\Committee\CommitteeDesignationController;
use App\Http\Controllers\Backend\Committee\CommitteeMemberController;
use App\Http\Controllers\Backend\Conference\ConferenceAddonController;
use App\Http\Controllers\Backend\Conference\ConferenceCertificateController;
use App\Http\Controllers\Backend\Conference\ConferenceController;
use App\Http\Controllers\Backend\Conference\ConferenceMemberTypePriceController;
use App\Http\Controllers\Backend\Conference\ConferenceRegistrationController;
use App\Http\Controllers\Backend\Conference\ConferenceSettingController;
use App\Http\Controllers\Backend\Conference\PassSettingController;
use App\Http\Controllers\Backend\Conference\AccommodationManagementController;
use App\Http\Controllers\Backend\Dashboard\ConferenceDashboardController;
use App\Http\Controllers\Backend\Download\DownloadController;
use App\Http\Controllers\Backend\Faq\FaqCategoryController;
use App\Http\Controllers\Backend\Faq\FaqController;
use App\Http\Controllers\Backend\LogReport\logActivityController;
use App\Http\Controllers\Backend\Notice\NoticeController;
use App\Http\Controllers\Backend\OfficialMessage\OfficialMessageController;
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
use App\Http\Controllers\Backend\Conference\ArticleTypeController;
use App\Http\Controllers\Backend\Template\EmailTemplateController;
use App\Http\Controllers\Backend\User\SignupUserController;
use App\Http\Controllers\Backend\UserManagement\RoleController;
use App\Http\Controllers\Backend\Workshop\PassSetting\WorkshopPassSettingController;
use App\Http\Controllers\Backend\Workshop\Workshop\WorkshopController;
use App\Http\Controllers\Backend\Workshop\WorkshopRegistration\WorkshopRegistrationController;
use App\Http\Controllers\Backend\Workshop\WorkshopTrainer\WorkshopTrainerController;
use App\Http\Controllers\Backend\Workshop\WorkshopCertificateController;
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
        Route::post('/conference/{conference}/archive', [ConferenceController::class, 'archive'])->name('conference.archive');
        Route::post('/conference/{conference}/unarchive', [ConferenceController::class, 'unarchive'])->name('conference.unarchive');
    });
    Route::post('/conference/show', [ConferenceController::class, 'view'])->name('conference.show');

    //conference open portal route 
    Route::prefix('/society/{society}/conference/{conference}')->group(function () {
        Route::get('/dashboard', [ConferenceController::class, 'openConferencePortal'])->name('conference.openConferencePortal');
        Route::get('/dashboard/attendance-status', [ConferenceController::class, 'viewAttendanceStatus'])->name('conference.viewAttendanceStatus');
        Route::get('/dashboard/attendance-status/export', [ConferenceController::class, 'exportAttendanceStatus'])->name('conference.exportAttendanceStatus');
        Route::get('/dashboard/submissions-chart', [ConferenceController::class, 'submissionsChart'])->name('conference.submissionsChart');

        // Accommodation Management Routes
        Route::controller(AccommodationManagementController::class)->middleware(['auto.conf.permission'])->prefix('accommodation')->name('conference.accommodation.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/{accommodation}', 'show')->name('show');
            Route::post('/send-reminder', 'sendReminder')->name('sendReminder');
            Route::post('/create-for-invited', 'createForInvited')->name('createForInvited');
            Route::post('/store-for-invited', 'storeForInvited')->name('storeForInvited');
        });
    });
    Route::get('/conference/stats', [ConferenceController::class, 'getStats'])->name('conference.stats');

    //conference route ended
    //conference member type price route started
    Route::post('/conference/price-form', [ConferenceMemberTypePriceController::class, 'priceForm'])->name('conference.priceForm');
    Route::post('/conference/price-submit', [ConferenceMemberTypePriceController::class, 'priceSubmit'])->name('conference.priceSubmit');
    //conference member type price route ended

    Route::post('conference-setting', [ConferenceSettingController::class, 'conferenceSetting'])->name('conference.setting');
    Route::post('conference-setting-submit', [ConferenceSettingController::class, 'conferenceSettingSubmit'])->name('conference.setting.submit');

    Route::post('conference/add-on', [ConferenceAddonController::class, 'addOn'])->name('conference.addon');
    Route::post('conference/add-on-submit', [ConferenceAddonController::class, 'addOnSubmit'])->name('conference.addon.submit');

    Route::controller(ConferenceRegistrationController::class)->name('conference.conference-registration.')->middleware(['auto.conf.permission', 'feature:conference-registration-management'])->prefix('/society/{society}/conference/{conference}/conference-registration')->group(function () {
        Route::get('/registrant', 'index')->name('index');
        Route::post('/view-data', 'show')->name('show'); 
        Route::get('/registrant/{registrant}/edit', 'edit')->name('edit');
        Route::put('/registrant/{registrant}', 'update')->name('update');
        Route::delete('/registrant/{registrant}/delete-voucher', 'deleteVoucher')->name('deleteVoucher');
        Route::delete('/accompany-person/{accompanyPerson}', 'deleteAccompanyPerson')->name('deleteAccompanyPerson');
        Route::get('/register-for-exceptional-case', 'registerForExceptionalCase')->name('registerForExceptionalCase');
        Route::post('/register-for-exceptional-case-submit', 'registerForExceptionalCaseSubmit')->name('registerForExceptionalCaseSubmit');
        Route::get('/get-user-member-type-addons', 'getUserMemberTypeAddons')->name('getUserMemberTypeAddons');
        Route::get('/get-member-type-addons', 'getMemberTypeAddons')->name('getMemberTypeAddons');
        Route::post('/add-person', 'addPerson')->name('addPerson');
        Route::post('/add-person-submit', 'addPersonSubmit')->name('addPersonSubmit');
        Route::post('/import-conference-registrant', 'importExcel')->name('importExcel');
        Route::post('/import-conference-registrant-submit', 'importExcelSubmit')->name('importExcelSubmit');
        Route::post('/convert-registrant-type', 'convertRegistrantType')->name('convertRegistrantType');
        Route::post('/convert-registrant-type-submit', 'convertRegistrantTypeSubmit')->name('convertRegistrantTypesubmit');
        Route::post('/verify-registrant', 'verifyForm')->name('verifyForm');
        Route::post('/verify-registrant-submit', 'verifyRegistrant')->name('verifyRegistrant');
        Route::get('/registration-or-invitation', 'registrationOrInvitation')->name('registrationOrInvitation');
        Route::post('/registration-or-invitation-submit', 'registrationOrInvitationSubmit')->name('registrationOrInvitationSubmit');
        Route::get('/exportExcel',  'excelExport')->name('excelExport');
        Route::get('/generate-pass',  'generatePass')->name('generatePass');
        Route::get('/generate-certificate/{conferenceRegistration}',  'generateCertificate')->name('generateCertificate');
        Route::get('/generate-individual-pass/{conferenceRegistration}', 'generateIndividualPass')->name('generateIndividualPass');
        Route::get('/download-voucher/{conferenceRegistration}', 'downloadVoucher')->name('downloadVoucher');
        Route::post('/generate-dummy-pass', 'generateDummyPass')->name('generateDummyPass');
        Route::post('/update-registration-ids', 'updateRegistrationIds')->name('updateRegistrationIds');
        Route::get('/bulk-email', 'showBulkEmailForm')->name('bulkEmail');
        Route::post('/bulk-email', 'sendBulkEmail')->name('sendBulkEmail');
        Route::get('/registrant/{registrant}/send-email', 'showIndividualEmailForm')->name('showIndividualEmail');
        Route::post('/registrant/{registrant}/send-email', 'sendIndividualEmail')->name('sendIndividualEmail');
        Route::delete('/registrant/destroy/{registrant}', 'destroy')->name('registrant.destroy');
    });

    Route::controller(ConferenceRegistrationController::class)->name('conference.conference-registration.')->group(function () {
        Route::post('/participant/take-attendance', 'takeAttendance')->name('takeAttendance');
        Route::post('/participant/take-meal', 'takeMeal')->name('takeMeal');
        Route::post('/participant/take-conference-kit', 'takeConferenceKit')->name('takeConferenceKit');
        Route::post('/participant/vote', 'vote')->name('vote');
    });

    Route::controller(PassSettingController::class)->middleware('auto.conf.permission')->prefix('/society/{society}/conference/{conference}/conference-registration')->group(function () {
        Route::resource('pass-setting', PassSettingController::class);
    });

    Route::prefix('/society/{society}/conference/{conference}/conference-registration')->middleware('auto.conf.permission')->group(function () {
        Route::resource('conference-certificate', ConferenceCertificateController::class);
    });

    //offical message
    Route::prefix('/society/{society}/conference/{conference}/')->group(function () {
        Route::post('official-message/update-order', [OfficialMessageController::class, 'updateOrder'])->name('official-message.update-order');
        Route::resource('official-message', OfficialMessageController::class);
    });

    Route::get('/conference-certificate/{conferenceCertificate}/signature/{signature}', [ConferenceCertificateController::class, 'deleteImage'])->name('conference-certificate.signature.remove');


    //submission setting route started
    Route::controller(SubmissionSettingController::class)->middleware(['auto.conf.permission', 'feature:abstract-submission-management'])->prefix('/society/{society}/conference/{conference}/submission')->name('submission.')->group(function () {
        Route::get('/submission-setting', 'index')->name('setting');
        Route::post('/setting-submit', 'store')->name('settingSubmit');
    });
    //submission setting route ended

    //submission category/major track route start
    Route::controller(SubmissionCategoryMajorTrackContoller::class)->middleware(['auto.conf.permission', 'feature:abstract-submission-management'])->prefix('/society/{society}/conference/{conference}/submission/submission-cateogry-majortrack')->name('submission.category-majortrack.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{submissionCategoryMajortrack}', 'edit')->name('edit');
        Route::patch('/update/{submissionCategoryMajortrack}', 'update')->name('update');
        Route::delete('/destroy/{submissionCategoryMajortrack}', 'destroy')->name('destroy');
    });

    //article type route start
    Route::controller(ArticleTypeController::class)->middleware(['auto.conf.permission', 'feature:abstract-submission-management'])->prefix('/society/{society}/conference/{conference}/submission/article-type')->name('articleType.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{articleType}', 'edit')->name('edit');
        Route::patch('/update/{articleType}', 'update')->name('update');
        Route::delete('/destroy/{articleType}', 'destroy')->name('destroy');
        Route::post('/setting', 'setting')->name('setting');
        Route::post('/setting-submit', 'settingSubmit')->name('settingSubmit');
        Route::post('/update-order', 'updateOrder')->name('update-order');
    });
    //article type route end

    //contribution route start
    Route::controller(\App\Http\Controllers\Backend\Submission\ContributionController::class)->middleware(['auto.conf.permission', 'feature:abstract-submission-management'])->prefix('/society/{society}/conference/{conference}/submission/contribution')->name('contribution.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::patch('/update/{contribution}', 'update')->name('update');
        Route::delete('/destroy/{contribution}', 'destroy')->name('destroy');
    });
    //contribution route end


    //Submission Route Started
    Route::controller(SubmissionController::class)->middleware(['auto.conf.permission', 'feature:abstract-submission-management'])->prefix('/society/{society}/conference/{conference}/submission')->name('submission.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/view-submissions', 'viewSubmissions')->name('viewSubmissions');
        Route::post('show', 'show')->name('show');
        Route::get('edit/{submission}', 'edit')->name('edit');
        Route::patch('update/{submission}', 'update')->name('update');
        Route::post('/forward-to-expert-form', 'expertForwardForm')->name('expertForwardForm');
        Route::post('/forward-to-expert', 'expertForward')->name('expertForward');
        Route::post('/bulk-forward-to-expert-form', 'bulkExpertForwardForm')->name('bulkExpertForwardForm');
        Route::post('/bulk-forward-to-expert', 'bulkExpertForward')->name('bulkExpertForward');
        Route::post('/bulk-update-deadline-form', 'bulkUpdateDeadlineForm')->name('bulkUpdateDeadlineForm');
        Route::post('/bulk-update-deadline', 'bulkUpdateDeadline')->name('bulkUpdateDeadline');
        Route::post('/sent-to-author-form', 'sentToAuthorForm')->name('sentToAuthorForm');
        Route::post('/sentToAuthor', 'sentToAuthor')->name('sentToAuthor');
        Route::get('/{submission}/view-discussion', 'viewDiscussion')->name('viewDiscussion');
        Route::get('/convert-presentation-type-request/{id}', 'convertPresentationTypeRequest')->name('convertPresentationTypeRequest');
        Route::post('viewScore', 'viewScore')->name('viewScore');
        Route::post('send-mail', 'sendMail')->name('sendMail');
        Route::post('send-mail-submit', 'sendMailSubmit')->name('sendMailSubmit');
        Route::get('/get-users', 'getUsersByTypeAndPresentation')->name('get.users');
        Route::get('/export-word', 'exportWord')->name('export.word');
        Route::get('/export-excel', 'exportExcel')->name('export.excel');
        Route::get('/get-author/{id}', 'getAuthors')->name('getAuthors');
        Route::delete('/submission/destroy/{submission}', 'destroy')->name('submission.destroy');
    });
    

    Route::prefix('/society/{society}/conference/{conference}')->group(function () {
        Route::controller(AuthorController::class)->middleware(['auto.conf.permission', 'feature:abstract-submission-management'])->prefix('/submission/{submission}')->name('submission.author.')->group(function () {
            Route::get('/author', 'index')->name('index');
        });
    });
    //Submission Route Ended

    //Scientific Session route started
    Route::prefix('/society/{society}/conference/{conference}')->middleware(['auto.conf.permission', 'feature:scientific-session-management'])->group(function () {
        Route::resource('/scientific-session', ScientificSessionController::class)->except('show');
        Route::get('/schedule-session', [ScientificSessionController::class, 'scheduleSession'])->name('scheduleSession');
        Route::post('/scientific-session/upload-pdf', [ScientificSessionController::class, 'uploadPdf'])->name('scientific-session.upload-pdf');
        Route::delete('/scientific-session/delete-pdf', [ScientificSessionController::class, 'deletePdf'])->name('scientific-session.delete-pdf');
    });
    //Scientific Session route ended

    //Scientific Session Poll route started
    Route::prefix('/society/{society}/conference/{conference}')->middleware(['auto.conf.permission', 'feature:scientific-session-management'])->group(function () {
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
    Route::prefix('/society/{society}/conference/{conference}')->middleware(['auto.conf.permission', 'feature:scientific-session-management'])->group(function () {
        Route::resource('/scientific-session/category', ScientificSessionCategoryController::class)->except('show');
    });
    //Scientific Session category route  End

    //Hall route started
    Route::prefix('/society/{society}/conference/{conference}')->middleware(['auto.conf.permission', 'feature:scientific-session-management'])->group(function () {
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
            Route::post('login-history', 'loginHistory')->name('loginHistory');
            Route::post('/add-user-form', 'addUserForm')->name('addUserForm');
            Route::post('/add-user-submit', 'addUserSubmit')->name('addUserSubmit');
        });
    });

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::post('/workshop/update-order', [WorkshopController::class, 'updateOrder'])->name('workshop.update-order');
        Route::post('/workshop/{workshop}/toggle-publish', [WorkshopController::class, 'togglePublish'])->name('workshop.toggle-publish');
        Route::resource('/workshop', WorkshopController::class)->except('show');
        Route::controller(WorkshopController::class)->name('workshop.')->prefix('/workshop')->group(function () {
            Route::post('/view-data', 'view')->name('view');
            Route::post('/view-rating', 'viewRating')->name('view.rating');
            Route::post('/allocate-price-form', 'allocatePriceForm')->name('allocatePriceForm');
            Route::post('/allocate-price-submit', 'allocatePriceSubmit')->name('allocatePriceSubmit');
            Route::post('/{workshop_approve}/approve', 'approve')->name('approve');
            Route::post('/{workshop_reject}/reject', 'reject')->name('reject');
            Route::post('/{workshop_request_correction}/request-correction', 'requestCorrection')->name('requestCorrection');
            Route::post('/send-mail', 'sendMail')->name('sendMail');
            Route::post('/send-mail-submit', 'sendMailSubmit')->name('sendMailSubmit');
            Route::get('/get-users', 'getUsersByWorkshopAndType')->name('get.users');
            Route::get('/export-registrations', 'exportRegistrations')->name('export.registrations');
            Route::get('/export-trainers', 'exportTrainers')->name('export.trainers');
        });

        Route::controller(WorkshopRegistrationController::class)->name('workshop.workshop-registration.')->prefix('/workshop/workshop-registration')->group(function () {
            Route::get('/register-for-exceptional-case', 'registerForExceptionalCase')->name('registerForExceptionalCase');
            Route::post('/register-for-exceptional-case-submit', 'registerForExceptionalCaseSubmit')->name('registerForExceptionalCaseSubmit');
            Route::get('/register-for-new-user', 'registerForNewUser')->name('registerForNewUser');
            Route::post('/register-for-new-user-submit', 'registerForNewUserSubmit')->name('registerForNewUserSubmit');
            Route::get('/{workshop}', 'index')->name('index');
            Route::post('/verify-form', 'verifyForm')->name('verifyForm');
            Route::post('/verify', 'verify')->name('verify');
            Route::get('/download-voucher/{workshopRegistration}', 'downloadVoucher')->name('downloadVoucher');
            Route::post('/view', 'view')->name('view');
            Route::get('/edit/{workshop}/{registration}', 'edit')->name('edit');
            Route::put('/update/{workshop}/{registration}', 'update')->name('update');
            Route::delete('/destroy/{workshop}/{registration}', 'destroy')->name('destroy');
        });


        Route::controller(WorkshopTrainerController::class)->name('workshop.workshop-trainer.')->prefix('/workshop/workshop-trainer')->group(function () {
            Route::get('/{workshop}', 'index')->name('index');
            Route::get('{workshop}/create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{workshop}/{trainer}', 'edit')->name('edit');
            Route::any('update/{trainer}', 'update')->name('update');
            Route::delete('destroy/{workshop}/{trainer}', 'destroy')->name('destroy');
        });

        Route::controller(WorkshopCertificateController::class)->name('workshop-certificate.')->prefix('/workshop/{workshop}/workshop-certificate')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{workshop_certificate}/edit', 'edit')->name('edit');
            Route::match(['put', 'patch'], '/{workshop_certificate}/update', 'update')->name('update');
            Route::delete('/{workshop_certificate}/destroy', 'destroy')->name('destroy');
        });

        Route::resource('workshop/workshop-pass-settings', WorkshopPassSettingController::class)->middleware('auto.conf.permission');


        Route::resource('/committee/committe-designation', CommitteeDesignationController::class)->except('show');

        Route::post('/committee/update-order', [CommitteeController::class, 'updateOrder'])->name('committee.update-order');
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
            Route::post('get-registered-users', 'getRegisteredUsers')->name('getRegisteredUsers');
            Route::post('/update-order', 'updateOrder')->name('updateOrder');
        });
    });

    Route::get('workshop-registrant/generate-pass/{workshop}', [WorkshopRegistrationController::class, 'generatePass'])->name('workshop.generatePass');
    Route::get('workshop-registrant/generate-pass-batch/{workshop}', [WorkshopRegistrationController::class, 'generatePassBatch'])->name('workshop.generatePassBatch');
    Route::post('workshop-registrant/generate-dummy-pass/{workshop}', [WorkshopRegistrationController::class, 'generateDummyPass'])->name('workshop.generateDummyPass');

    Route::controller(WorkshopRegistrationController::class)->name('workshop.workshop-registration.')->group(function () {
        Route::post('/workshop/take-attendance', 'takeAttendance')->name('takeAttendance');
    });

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::post('/sponsor/sponsor-category/update-order', [SponsorCategoryController::class, 'updateOrder'])->name('sponsor-category.update-order');
        Route::resource('sponsor/sponsor-category', SponsorCategoryController::class)->except('show');
        Route::resource('/sponsor', SponsorController::class)->except('show');
        Route::get('generate-pass', [SponsorController::class, 'generatePass'])->name('sponsor.generaate-pass');
        Route::get('/sponsor/export-excel', [SponsorController::class, 'exportExcel'])->name('sponsor.export.excel');
        Route::get('/sponsor/update-registration-ids', [SponsorController::class, 'updateRegistrationIds'])->name('sponsor.update-registration-ids');
    });

    Route::controller(SponsorController::class)->name('sponsor.')->group(function () {
        Route::post('/take-attendance', 'takeAttendance')->name('takeAttendance');
        Route::post('/take-meal', 'takeMeal')->name('takeMeal');
    });


    Route::get('sponsor/change-status/{sponsor}', [SponsorController::class, 'changeStatus'])->middleware('auto.conf.permission')->name('sponsor.changeStatus');
    Route::post('/sponsor/add-participant', [SponsorController::class, 'addParticipant'])->name('sponsor.addParticipant');
    Route::post('/sponsor/add-participant-submit', [SponsorController::class, 'addParticipantSubmit'])->name('sponsor.addParticipantSubmit');

    Route::prefix('/society/{society}/conference/{conference}')->middleware('auto.conf.permission')->group(function () {
        Route::resource('faq', FaqController::class)->except('show');
        Route::resource('faq/faq-category', FaqCategoryController::class)->except('show');
        Route::post('/faq/update-order', [FaqController::class, 'updateOrder'])->name('faq.update-order');
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
        Route::post('/remove-role-form', [RoleController::class, 'removeRoleForm'])->name('removeRoleForm');
        Route::post('/remove-role-form-submit', [RoleController::class, 'removeRoleFormSubmit'])->name('removeRoleFormSubmit');
        Route::post('/roles/get-user-activity-log', [RoleController::class, 'getUserActivityLog'])->name('roles.getUserActivityLog');

        //Activity log
        Route::get('/activity-log', [logActivityController::class, 'index'])->name('activity-log.index');
    });

    Route::prefix('/society/{society}/conference/{conference}')->group(function () {
        Route::resource('email-template', EmailTemplateController::class)->except('show');
    });
});
// Accommodation routes for international participants
Route::group(['middleware' => ['auth']], function () {
    Route::get(
        '/my-society/{society}/conference/{conference}/accommodation',
        [App\Http\Controllers\Frontend\MainPage\AccommodationController::class, 'index']
    )
        ->name('my-society.conference.accommodation');

    Route::post(
        '/my-society/{society}/conference/{conference}/accommodation',
        [App\Http\Controllers\Frontend\MainPage\AccommodationController::class, 'store']
    )
        ->name('my-society.conference.accommodation.store');
});

Route::get('/participant/profile/{token}', [ConferenceRegistrationController::class, 'participantProfile']);
Route::get('workshop/participant/profile/{token}', [WorkshopRegistrationController::class, 'participantProfile']);
Route::get('/sponsor/profile/{token}', [SponsorController::class, 'sponsorProfile']);
