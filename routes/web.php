<?php

use App\Http\Controllers\Approval\OtherDataApprovalController;
use App\Http\Controllers\Backend\Accommodation\HotelController;
use App\Http\Controllers\Backend\Ckeditor\CkeditorController;
use App\Http\Controllers\Backend\Cms\BlogController as CmsBlogController;
use App\Http\Controllers\Backend\Cms\FeatureController;
use App\Http\Controllers\Backend\Cms\PageController;
use App\Http\Controllers\Backend\Cms\TestimonialController;
use App\Http\Controllers\Backend\Cms\WhyChooseUsController;
use App\Http\Controllers\Backend\Contact\ContactController as ContactContactController;
use App\Http\Controllers\Backend\Dashboard\DashboardController;
use App\Http\Controllers\Backend\Setting\SecurityController;
use App\Http\Controllers\Backend\User\UserController;
use App\Http\Controllers\Backend\UserManagement\PermissionController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Frontend\Conference\AuthController;
use App\Http\Controllers\Frontend\Conference\CommitteController;
use App\Http\Controllers\Frontend\Conference\HomeController as ConferenceHomeController;
use App\Http\Controllers\Frontend\Conference\NewsAndNoticeController;
use App\Http\Controllers\Frontend\Conference\ScientificSessionController;
use App\Http\Controllers\Frontend\Conference\SpeakerController;
use App\Http\Controllers\Frontend\Conference\WorkshopController;
use App\Http\Controllers\Frontend\InvitationController;
use App\Http\Controllers\Frontend\MainPage\AboutUsController;
use App\Http\Controllers\Frontend\MainPage\BlogController;
use App\Http\Controllers\Frontend\MainPage\ConferenceController;
use App\Http\Controllers\Frontend\MainPage\ContactController;
use App\Http\Controllers\Frontend\MainPage\HomeController;
use App\Http\Controllers\Frontend\MainPage\OurClientController;
use App\Http\Controllers\Frontend\MainPage\SolutionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

foreach (glob(__DIR__ . '/web/*.php') as $file) {
    require $file;
}

// Invitation routes (public access)
Route::prefix('invitation')->name('invitation.')->group(function () {
    Route::get('/{token}', [InvitationController::class, 'show'])->name('show');
    Route::post('/{token}/accept', [InvitationController::class, 'accept'])->name('accept');
    Route::post('/{token}/decline', [InvitationController::class, 'decline'])->name('decline');
});

// Route::get('/', function () {
//     return redirect()->route('login');
// });
//dashboard route
Route::middleware('auth', 'verified', 'check.subdomain')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/join-society', [DashboardController::class, 'joinSociety'])->name('joinSociety');
    Route::get('/get-society-member-type', [DashboardController::class, 'getMemberType'])->name('getMemberType');
    Route::post('/check-council-membership', [DashboardController::class, 'checkCouncilMembership'])->name('checkCouncilMembership');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('update-profile', [ProfileController::class, 'updateProfile'])->name('profile.update-profile');

    //ckedior route for fileupload
    Route::post('/ckeditor/file-upload', [CkeditorController::class, 'ckEditorUpload'])->name('ckeditor.fileUpload');

    Route::controller(CommonController::class)->group(function () {
        Route::post('/convert-usd-to-inr', 'convertUsdToInr')->name('convertUsdToInr');
        Route::get('/society/{society}/conference/{conference}/member-type', [CommonController::class, 'memberType'])->name('memberType');
    });
    Route::prefix('/society/{society}/conference/{conference}')->group(function () {
        Route::resource('/hotel', HotelController::class)->except('show');
    });
    Route::get('/hotel/{hotel}/image/{img}', [HotelController::class, 'deleteImage'])->name('hotel.image.delete');
    Route::get('/hotel/change-status/{hotel}', [HotelController::class, 'changeStatus'])->name('hotel.changeStatus');

    Route::resource('/permission', PermissionController::class)->middleware('check.superadmin');
    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::post('user/show', [UserController::class, 'show'])->name('user.show');
    Route::delete('user/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('user/join-society', [UserController::class, 'joinSociety'])->name('user.join-society');
    Route::post('user/join-society-submit', [UserController::class, 'joinSocietySubmit'])->name('user.joinSocietySubmit');

    Route::get('/setting/security', [SecurityController::class, 'index'])->name('security.index');
    Route::get('/society/{society}/setting/security', [SecurityController::class, 'index'])->name('security.index.society');
    Route::get('/society/{society}/conference/{conference}/setting/security', [SecurityController::class, 'index'])->name('security.index.full');
    Route::post('password-change', [SecurityController::class, 'passwordChange'])->name('security.password-change');


    Route::resource('/cms/blog', CmsBlogController::class)->middleware('check.superadmin')->except('show');
    Route::resource('/cms/feature', FeatureController::class)->middleware('check.superadmin')->except('show');
    Route::resource('/cms/testimonial', TestimonialController::class)->middleware('check.superadmin')->except('show');
    Route::resource('/cms/page', PageController::class)->middleware('check.superadmin')->except('show');
    Route::resource('/cms/why-choose-us', WhyChooseUsController::class)->middleware('check.superadmin')->except('show');
    Route::get('/contact', [ContactContactController::class, 'index'])->middleware('check.superadmin')->name('contact.index');
    Route::post('/contact/view-data', [ContactContactController::class, 'show'])->name('contact.show');
    Route::delete('/contact/delete/{contact:id}', [ContactContactController::class, 'destroy'])->name('contact.destroy');


    Route::get('approvals/others', [OtherDataApprovalController::class, 'index'])->name('admin.approvals.index');
    Route::post('approvals/approve', [OtherDataApprovalController::class, 'approve'])->name('admin.approvals.approve');
    Route::delete('approvals/reject', [OtherDataApprovalController::class, 'reject'])->name('admin.approvals.reject');
});


//====================================================== Frontend Route Started=====================================================================================
Route::get('get-member-type', [CommonController::class, 'getMemberType'])->name('getMemberTypes');
Route::middleware('check.subdomain')
    ->group(function () {

        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/about-us', [AboutUsController::class, 'index'])->name('about-us');
        Route::get('/solution', [SolutionController::class, 'index'])->name('solution');
        Route::get('/our-client', [OurClientController::class, 'index'])->name('our-client');
        Route::get('/our-client/{society_front:slug}', [OurClientController::class, 'detail'])->name('our-client.detail');
        Route::get('/blog', [BlogController::class, 'index'])->name('blog');
        Route::get('blog/{blog:slug}', [BlogController::class, 'singlePage'])->name('blog.single-page');
        Route::get('/contact-us', [ContactController::class, 'index'])->name('contact-us');
        Route::post('/contact-us-store', [ContactController::class, 'store'])->name('contact-us.store');
        Route::get('/conference', [ConferenceController::class, 'index'])->name('conference');
        Route::get('/conference-filter', [ConferenceController::class, 'filter'])->name('conference.filter');
    });

Route::prefix('conference/{conference_front:slug}')->middleware('check.subdomain')
    ->as('conference.')
    ->group(function () {
        Route::get('/', [ConferenceHomeController::class, 'index'])->name('name');
        Route::get('/speaker', [SpeakerController::class, 'index'])->name('speaker');
        Route::get('/committe', [CommitteController::class, 'index'])->name('committe');
        Route::get('/workshop', [WorkshopController::class, 'index'])->name('workshop');
        Route::get('/workshop/{workshop_front:slug}', [WorkshopController::class, 'singlePage'])->name('workshop.singlePage');
        Route::get('/scientific-session', [ScientificSessionController::class, 'index'])->name('scientific-session');
        Route::get('/news-and-notice', [NewsAndNoticeController::class, 'index'])->name('news-and-notice');
        Route::get('/register', [AuthController::class, 'register'])->name('register');
        Route::get('/terms-conditions', [ConferenceHomeController::class, 'termsConditions'])->name('terms-conditions');
        Route::get('/privacy-policy', [ConferenceHomeController::class, 'privacyPolicy'])->name('privacy-policy');
        Route::get('/hotel/{slug}', [ConferenceHomeController::class, 'hotelDetail'])->name('hotel.detail');
    });
