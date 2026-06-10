
<?php

use App\Http\Controllers\Backend\Payment\PaymentSettingController;
use App\Http\Controllers\Backend\Society\DepartmentController;
use App\Http\Controllers\Backend\Society\DesignationController;
use App\Http\Controllers\Backend\Society\InstitutionController;
use App\Http\Controllers\Backend\Society\MemberTypeController;
use App\Http\Controllers\Backend\Society\NamePrefixController;
use App\Http\Controllers\Backend\Conference\RegistrantTypeController;
use App\Http\Controllers\Backend\Society\SocietyController;
use App\Http\Controllers\Backend\Society\SocietyNamePrefixController;
use App\Http\Controllers\Backend\Society\SocietyInstitutionController;
use App\Http\Controllers\Backend\Society\SocietyDesignationController;
use App\Http\Controllers\Backend\Society\SocietyDepartmentController;
use App\Http\Controllers\Backend\Society\SocietySettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check.subdomain'])->group(function () {
    //society controller start    
    Route::resource('/society', SocietyController::class)->middleware('check.superadmin')->except('show');

    Route::post('society-setting', [SocietySettingController::class, 'societySetting'])->name('society.setting');
    Route::post('society-setting-submit', [SocietySettingController::class, 'societySettingSubmit'])->name('society.setting.submit');

    Route::get('/society/{society}/dashboard', [SocietyController::class, 'dashboard'])->name('society.dashboard');
    Route::post('/society/{society}/dashboard-data', [SocietyController::class, 'getDashboardData'])->name('society.dashboard.data');
    Route::post('/society/show', [SocietyController::class, 'view'])->middleware('check.superadmin')->name('society.show');
    Route::get('/view-society-detail/{slug}', [SocietyController::class, 'viewDetailByAdmin'])->middleware('check.societyadmin')->name('society.viewDetailByAdmin');
    //society controller end

    //society member type start
    Route::prefix('/society/{society}')->group(function () {
        Route::resource('/memberType', MemberTypeController::class)->middleware('check.societyadmin')->except('show',);
        Route::post('/memberType/update-order', [MemberTypeController::class, 'updateOrder'])
            ->name('memberType.update-order')
            ->middleware('check.societyadmin');
        Route::get('/fetch-member-types', [MemberTypeController::class, 'fetchExternalMemberTypes'])
            ->name('memberType.fetch');
        
        // Society Name Prefix routes
        Route::controller(SocietyNamePrefixController::class)->middleware('check.societyadmin')->group(function () {
            Route::get('/name-prefix', 'index')->name('society.name-prefix.index');
            Route::post('/name-prefix', 'update')->name('society.name-prefix.update');
        });

        // Society Institution routes
        Route::controller(SocietyInstitutionController::class)->middleware('check.societyadmin')->group(function () {
            Route::get('/institution', 'index')->name('society.institution.index');
            Route::post('/institution', 'update')->name('society.institution.update');
        });

        // Society Designation routes
        Route::controller(SocietyDesignationController::class)->middleware('check.societyadmin')->group(function () {
            Route::get('/designation', 'index')->name('society.designation.index');
            Route::post('/designation/update-order', 'updateOrder')->name('society.designation.update-order');
            Route::post('/designation', 'update')->name('society.designation.update');
        });

        // Society Department routes
        Route::controller(SocietyDepartmentController::class)->middleware('check.societyadmin')->group(function () {
            Route::get('/department', 'index')->name('society.department.index');
            Route::post('/department/update-order', 'updateOrder')->name('society.department.update-order');
            Route::post('/department', 'update')->name('society.department.update');
        });
    });
    //society member type end 

    //name prefix route started
    Route::resource('/name-prefix', NamePrefixController::class)->middleware('check.superadmin')->except('show');
    //name prefix route ended

    //name institution started
    Route::resource('/institution', InstitutionController::class)->middleware('check.superadmin')->except('show');
    Route::post('/institution-merge-form', [InstitutionController::class, 'mergeForm'])->name('institution.mergeForm')->middleware('check.superadmin');
    Route::post('/institution-merge-submit', [InstitutionController::class, 'mergeSubmit'])->name('institution.mergeSubmit')->middleware('check.superadmin');
    //name institution ended

    //name designation started
    Route::resource('/designation', DesignationController::class)->middleware('check.superadmin')->except('show');
    //name designation ended

    //name department started
    Route::resource('/department', DepartmentController::class)->middleware('check.superadmin')->except('show');
    //name department ended

    //registration type started
    Route::controller(RegistrantTypeController::class)->middleware('check.superadmin')->group(function () {
        Route::get('/registrant-type', 'globalIndex')->name('registrant-type.global.index');
        Route::post('/registrant-type', 'globalStore')->name('registrant-type.global.store');
        Route::put('/registrant-type/{registrantType}', 'globalUpdate')->name('registrant-type.global.update');
        Route::delete('/registrant-type/{registrantType}', 'globalDestroy')->name('registrant-type.global.destroy');
    });
    //registration type ended


    //payment setting controller stared
    Route::prefix('/society/{society}')->middleware('check.societyadmin')->group(function () {
        Route::controller(PaymentSettingController::class)->prefix('/payment')->name('payment.')->group(function () {
            Route::get('/payment-setting', 'index')->name('setting');
            Route::post('/setting/submit', 'store')->name('setting.submit');
        });
    });
});
