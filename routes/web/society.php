
<?php

use App\Http\Controllers\Backend\Payment\PaymentSettingController;
use App\Http\Controllers\Backend\Society\DepartmentController;
use App\Http\Controllers\Backend\Society\DesignationController;
use App\Http\Controllers\Backend\Society\InstitutionController;
use App\Http\Controllers\Backend\Society\MemberTypeController;
use App\Http\Controllers\Backend\Society\NamePrefixController;
use App\Http\Controllers\Backend\Society\SocietyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    //society controller start    
    Route::resource('/society', SocietyController::class)->middleware('check.superadmin')->except('show');
    Route::get('/society/{society}/dashboard', [SocietyController::class, 'dashboard'])->name('society.dashboard');
    Route::post('/society/show', [SocietyController::class, 'view'])->middleware('check.superadmin')->name('society.show');
    Route::get('/view-society-detail/{slug}', [SocietyController::class, 'viewDetailByAdmin'])->middleware('check.societyadmin')->name('society.viewDetailByAdmin');
    //society controller end

    //society member type start
    Route::prefix('/society/{society}')->group(function () {
        Route::resource('/memberType', MemberTypeController::class)->middleware('check.societyadmin')->except('show', 'destroy');
    });
    //society member type end

    //name prefix route started
    Route::resource('/name-prefix', NamePrefixController::class)->middleware('check.superadmin')->except('show');
    //name prefix route ended

    //name institution started
    Route::resource('/institution', InstitutionController::class)->middleware('check.superadmin')->except('show');
    //name institution ended

    //name designation started
    Route::resource('/designation', DesignationController::class)->middleware('check.superadmin')->except('show');
    //name designation ended

    //name department started
    Route::resource('/department', DepartmentController::class)->middleware('check.superadmin')->except('show');
    //name department ended

 
    //payment setting controller stared
    Route::prefix('/society/{society}')->middleware('check.societyadmin')->group(function () {
        Route::controller(PaymentSettingController::class)->prefix('/payment')->name('payment.')->group(function () {
            Route::get('/payment-setting', 'index')->name('setting');
            Route::post('/setting/submit', 'store')->name('setting.submit');
        });
    });
});
