<?php

use App\Http\Controllers\Api\ConferenceRegistrationController;
use App\Http\Controllers\Api\MemberDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/conference-registrations', [ConferenceRegistrationController::class, 'store']);
Route::get('/get-member-data', [MemberDataController::class, 'getMemberData']);
Route::get('/get-member-type-price', [MemberDataController::class, 'getMemberTypePrice']);
