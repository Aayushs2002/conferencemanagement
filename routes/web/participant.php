<?php

use App\Http\Controllers\Backend\Participant\AuthorController;
use App\Http\Controllers\Backend\Participant\ConferenceDashboardController;
use App\Http\Controllers\Backend\Participant\ConferenceRegistrationController;
use App\Http\Controllers\Backend\Participant\MySocietyController;
use App\Http\Controllers\Backend\Participant\MyWorkshopRegistrationController;
use App\Http\Controllers\Backend\Participant\PaymentContoller;
use App\Http\Controllers\Backend\Participant\SubmissionController;
use App\Http\Controllers\Backend\Participant\WorkshopController;
use App\Http\Controllers\Backend\Participant\WorkshopPaymentController;
use App\Http\Controllers\Backend\Participant\WorkshopRegistrationController;
use App\Http\Controllers\Backend\Participant\WorkshopTrainerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::controller(MySocietyController::class)->group(function () {
        Route::get('/my-society', 'index')->name('my-society.index');
        Route::get('/my-society/{society}', 'detail')->name('my-society.detail');
        Route::get('/my-society/{society}/conference', 'conference')->name('my-society.conference');
        Route::post('/join-society-submit', 'joinSocietySubmit')->name('joinSocietySubmit');
    });

    Route::get('/my-society/conference/{conference}/dashboard', [ConferenceDashboardController::class, 'index'])->name('my-society.conference.dashboard');

    Route::controller(ConferenceRegistrationController::class)->prefix('/my-society/{society}/conference/{conference}')->name('my-society.conference.')->group(function () {
        Route::get('/conference-registration', 'index')->name('index');
        Route::get('/conference-registration/create', 'create')->name('create');
        Route::post('/conference-registration/store', 'store')->name('store');
        Route::post('/check-submission', 'checkSubmission')->name('checkSubmission');
        Route::post('/online-payment-submit', 'onlinePaymentSubmit')->name('submit');
        Route::patch('/update-conference-registation', 'updateRegistration')->name('updateRegistration');
    });

    Route::get('/get-workshop-pricing', [ConferenceRegistrationController::class, 'getWorkshopPricing'])
        ->name('getWorkshopPricing');

    Route::controller(PaymentContoller::class)->prefix('/my-society/{society}/conference/{conference}')->name('my-society.conference.')->group(function () {
        //fonepay route
        Route::post('/fonepay', 'fonepay')->name('fonepay');
        Route::get('/conference-registration/fone-pay/success', 'fonePaySuccess')->name('fonePaySuccess');
        //Moco Route
        Route::post('/moco', 'moco')->name('moco');
        Route::post('/moco/check-status', 'mocoCheckStatus')->name('mocoCheckStatus');
        Route::get('/moco/payment-success', 'mocoSuccess')->name('mocoSuccess');
        //Esewa Route
        Route::post('/esewa', 'esewa')->name('esewa');
        Route::get('/esewa/success', 'esewaSuccess')->name('esewaSuccess');
        Route::get('/esewa/error', 'esewaError')->name('esewaError');

        //Khati Route
        Route::post('/khalti', 'khalti')->name('khalti');
        Route::get('/khalti/success', 'khaltiSuccess')->name('khaltiSuccess');

        //Himalayan Bank Payment
        Route::post('/international-payment', 'internationalPayment')->name('internationalPayment');
        Route::get('/international-payment-result/success-process', 'internationalPaymentResultSuccessProcess')->name('internationalPaymentResultSuccessProcess');
        Route::get('/international-payment-result/success', 'internationalPaymentResultSuccess')->name('internationalPaymentResultSuccess');
        Route::get('/international-payment-result/fail', 'internationalPaymentResultFail')->name('internationalPaymentResultFail');
        Route::get('/international-payment-result/cancel', 'internationalPaymentResultCancel')->name('internationalPaymentResultCancel');
        Route::get('/international-payment-result/backend', 'internationalPaymentResultBackend')->name('internationalPaymentResultBackend');
    });

    Route::controller(SubmissionController::class)->prefix('/my-society/{society}/conference/{conference}')->name('my-society.conference.submission.')->group(function () {
        Route::get('/submission', 'index')->name('index');
        Route::get('/review-submission', 'submissionReview')->name('submissionReview');
        Route::get('/submission/create', 'create')->name('create');
        Route::post('/submission/store', 'store')->name('store');
        Route::get('/submission/{submission}/edit', 'edit')->name('edit');
        Route::patch('/submission/{submission}/update', 'update')->name('update');
        Route::post('/submission/view', 'view')->name('view');
        Route::post('/submission/review', 'review')->name('review');
        Route::post('/submission/review-submit', 'reviewSubmit')->name('reviewSubmit');
        Route::get('/submission/{submission}/view-discussion', 'viewDiscussion')->name('viewDiscussion');
        Route::get('/submission/convert-presentation-type/{id}', 'convertPresentationType')->name('convertPresentationType');
        Route::get('/submission/get-article-type-setting', 'getArticleTypeSetting')->name('get-article-type-setting');
    });

    Route::controller(AuthorController::class)->prefix('/my-society/{society}/conference/{conference}/submission')->name('my-society.conference.submission.author.')->group(function () {
        Route::get('{submission}/author', 'index')->name('index');
        Route::post('/author/create', 'create')->name('create');
        Route::post('/author/old-author', 'oldAuthor')->name('oldAuthor');
        Route::post('/author/store', 'store')->name('store');
        Route::any('/author/update/{author}', 'update')->name('update');
        Route::delete('/author/destroy/{author}', 'destroy')->name('destroy');
    });


    // Normal User Workshop Application Management
    Route::controller(WorkshopController::class)->prefix('/my-society/{society}/conference/{conference}')->name('my-society.conference.my-workshop.')->group(function () {
        Route::get('/my-workshops', 'index')->name('index');
        Route::get('/my-workshops/create', 'create')->name('create');
        Route::post('/my-workshops/store', 'store')->name('store');
        Route::get('/my-workshops/{workshop}/edit', 'edit')->name('edit');
        Route::patch('/my-workshops/{workshop}/update', 'update')->name('update');
        Route::delete('/my-workshops/{workshop}/destroy', 'destroy')->name('destroy');
        Route::post('/my-workshops/view-data', 'view')->name('view');
        Route::post('/my-workshops/allocate-price-form', 'allocatePriceForm')->name('allocatePriceForm');
        Route::post('/my-workshops/allocate-price-submit', 'allocatePriceSubmit')->name('allocatePriceSubmit');
    });

    // Participant Workshop Trainer Management (for their approved workshops)
    Route::controller(WorkshopTrainerController::class)->prefix('/my-society/{society}/conference/{conference}')->name('my-society.conference.my-workshop.trainer.')->group(function () {
        Route::get('/my-workshops/{workshop}/trainers', 'index')->name('index');
        Route::get('/my-workshops/{workshop}/trainers/create', 'create')->name('create');
        Route::post('/my-workshops/trainers/store', 'store')->name('store');
        Route::get('/my-workshops/{workshop}/trainers/{trainer}/edit', 'edit')->name('edit');
        Route::patch('/my-workshops/trainers/{trainer}/update', 'update')->name('update');
        Route::delete('/my-workshops/{workshop}/trainers/{trainer}/destroy', 'destroy')->name('destroy');
    });

    // Participant Workshop Registration Management (for their approved workshops)
    Route::controller(MyWorkshopRegistrationController::class)->prefix('/my-society/{society}/conference/{conference}')->name('my-society.conference.my-workshop.registration.')->group(function () {
        Route::get('/my-workshops/{workshop}/registrations', 'index')->name('index');
        Route::post('/my-workshops/registrations/view', 'view')->name('view');
        Route::post('/my-workshops/registrations/verify-form', 'verifyForm')->name('verifyForm');
        Route::post('/my-workshops/registrations/verify', 'verify')->name('verify');
    });

    Route::controller(WorkshopRegistrationController::class)->prefix('/my-society/{society}/conference/{conference}')->name('my-society.conference.workshop.')->group(function () {
        Route::get('/workshop-registration', 'index')->name('index');
        Route::post('/workshop-registration/submit-data', 'submitData')->name('submitData');
        Route::post('/workshop-registration/meal', 'meal')->name('meal');
        Route::post('/workshop-registration/mealSubmit', 'submitMealPreference')->name('submitMealPreference');
        Route::post('/workshop-registration/store/{workshop}', 'store')->name('store');
        Route::post('/workshop-registration/rating', 'rating')->name('rating');
        Route::post('/workshop-registration/submit-rating', 'submitRating')->name('submitRating');
    });

    //workshopt payment controller
    Route::controller(WorkshopPaymentController::class)->name('my-society.conference.workshop-registration.')->prefix('/my-society/{society}/conference/{conference}/workshop-registration')->group(function () {
        Route::post('/fonepay/{workshop}', 'fonePay')->name('fonePay');
        Route::get('/fone-pay/success', 'fonePaySuccess')->name('fonePaySuccess');

        //Moco Route
        Route::post('/moco/{workshop}', 'moco')->name('moco');
        Route::post('/moco/check/payment', 'mocoCheckStatus')->name('mocoCheckStatus');
        Route::get('/moco/payment-success', 'mocoSuccess')->name('mocoSuccess');

        //Esewa Route
        Route::post('/esewa/payment/{workshop}', 'esewa')->name('esewa');
        Route::get('/esewa/success', 'esewaSuccess')->name('esewaSuccess');
        Route::get('/esewa/error', 'esewaError')->name('esewaError');

        //Khati Route
        Route::post('/khalti/payment/{workshop}', 'khalti')->name('khalti');
        Route::get('/khalti/success', 'khaltiSuccess')->name('khaltiSuccess');

        //international payment
        Route::post('/international-payment/{workshop}', 'internationalPayment')->name('internationalPayment');
        Route::get('/international-payment-result/success-process', 'internationalPaymentResultSuccessProcess')->name('internationalPaymentResultSuccessProcess');
        Route::get('/international-payment-result/success', 'internationalPaymentResultSuccess')->name('internationalPaymentResultSuccess');
        Route::get('/international-payment-result/fail', 'internationalPaymentResultFail')->name('internationalPaymentResultFail');
        Route::get('/international-payment-result/cancel', 'internationalPaymentResultCancel')->name('internationalPaymentResultCancel');
        Route::get('/international-payment-result/backend', 'internationalPaymentResultBackend')->name('internationalPaymentResultBackend');
    });
});
