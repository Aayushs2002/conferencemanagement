<?php

use App\Http\Controllers\Backend\Accommodation\HotelController;
use App\Http\Controllers\Backend\Ckeditor\CkeditorController;
use App\Http\Controllers\Backend\Dashboard\DashboardController;
use App\Http\Controllers\Backend\UserManagement\PermissionController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocietyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

foreach (glob(__DIR__ . '/web/*.php') as $file) {
    require $file;
}

Route::get('/', function () {
    return redirect()->route('login');
});
//dashboard route
Route::middleware('auth', 'verified')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/join-society', [DashboardController::class, 'joinSociety'])->name('joinSociety');
    Route::get('/get-society-member-type', [DashboardController::class, 'getMemberType'])->name('getMemberType');
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
});
