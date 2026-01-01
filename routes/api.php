<?php

use App\Http\Controllers\Api\ConferenceApiController;
use App\Http\Controllers\Api\ConferenceRegistrationController;
use App\Http\Controllers\Api\MemberDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Existing routes
Route::post('/conference-registrations', [ConferenceRegistrationController::class, 'store']);
Route::get('/get-member-data', [MemberDataController::class, 'getMemberData']);
Route::get('/get-member-type-price', [MemberDataController::class, 'getMemberTypePrice']);

// Conference API Routes (by slug)
Route::prefix('conference/{slug}')->group(function () {
    // Get complete conference details
    Route::get('/', [ConferenceApiController::class, 'getConference']);
    
    // Get basic conference info (lightweight)
    Route::get('/basic', [ConferenceApiController::class, 'getConferenceBasicInfo']);
    
    // Get all conference data at once (complete data)
    Route::get('/all', [ConferenceApiController::class, 'getAllConferenceData']);
    
    // Get specific sections
    Route::get('/about', [ConferenceApiController::class, 'getAboutConference']);
    Route::get('/accommodation', [ConferenceApiController::class, 'getAccommodation']);
    Route::get('/hotels', [ConferenceApiController::class, 'getAccommodation']); // Alias
    Route::get('/workshops', [ConferenceApiController::class, 'getWorkshops']);
    Route::get('/news-notices', [ConferenceApiController::class, 'getNewsNotices']);
    Route::get('/downloads', [ConferenceApiController::class, 'getDownloads']);
    Route::get('/scientific-sessions', [ConferenceApiController::class, 'getScientificSessions']);
    Route::get('/submission-tracks', [ConferenceApiController::class, 'getScientificSessions']); // Alias
    Route::get('/article-types', [ConferenceApiController::class, 'getArticleTypes']);
    Route::get('/committee-members', [ConferenceApiController::class, 'getCommitteeMembers']);
    Route::get('/official-messages', [ConferenceApiController::class, 'getOfficialMessages']);
    Route::get('/settings', [ConferenceApiController::class, 'getConferenceSettings']);
});
