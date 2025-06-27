<?php

namespace App\Providers;

use App\Models\Conference\Author;
use App\Models\Conference\Conference;
use App\Models\Conference\Submission;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Vinkla\Hashids\Facades\Hashids;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Route::bind('society', function ($value) {
            $id = Hashids::decode($value)[0] ?? null;
            abort_if(!$id, 404);
            return Society::findOrFail($id);
        });

        Route::bind('conference', function ($value) {
            $id = Hashids::decode($value)[0] ?? null;
            abort_if(!$id, 404);
            return Conference::findOrFail($id);
        });

        Route::bind('submission', function ($value) {
            $id = Hashids::decode($value)[0] ?? null;
            abort_if(!$id, 404);
            return Submission::findOrFail($id);
        });

        Route::bind('author', function ($value) {
            $id = Hashids::decode($value)[0] ?? null;
            abort_if(!$id, 404);
            return Author::findOrFail($id);
        });

        Route::bind('workshop', function ($value) {
            $id = Hashids::decode($value)[0] ?? null;
            abort_if(!$id, 404);
            return Workshop::findOrFail($id);
        });
    }
}
