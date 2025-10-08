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

        // Bind all admin models via hashids
        $this->bindHashid('society', Society::class);
        $this->bindHashid('conference', Conference::class);
        $this->bindHashid('submission', Submission::class);
        $this->bindHashid('author', Author::class);
        $this->bindHashid('workshop', Workshop::class);
    }

    /**
     * Bind a route parameter to a model resolved by Hashid.
     */
    protected function bindHashid(string $parameter, string $model): void
    {
        Route::bind($parameter, function ($value) use ($model, $parameter) {
            $decoded = Hashids::decode($value);
            $id = $decoded[0] ?? null;

            // Use is_null so we don't accidentally treat id==0 as invalid
            abort_if(is_null($id), 404, "Invalid {$parameter} id");

            return $model::findOrFail($id);
        });
    }
}
