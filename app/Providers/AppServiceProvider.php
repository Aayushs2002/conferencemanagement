<?php

namespace App\Providers;

use App\Models\User\Country;
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use Illuminate\Support\Facades\View;
use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

class AppServiceProvider extends ServiceProvider
{
    /** 
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $sharedData = [
            'countries'    => Country::whereStatus(1)->get(),
            'name_prefiexs' => NamePrefix::whereStatus(1)->get(),
            'departments'  => Department::whereStatus(1)->get(),
            'designations' => Designation::whereStatus(1)->get(),
            'institutions' => Institution::whereStatus(1)->get(),
            'societies' => Society::whereStatus(1)->get(),
        ];

        View::share($sharedData);

        RedirectIfAuthenticated::redirectUsing(function ($request) {
        
            if (current_user()->type == 2) {
                return route('society.dashboard', current_user()->societies->first());
            } else { 
                return route('dashboard');
            }
        });

        // Global eager loading to prevent N+1 queries
        $this->configureEagerLoading();
    }

    /**
     * Configure global eager loading for models to prevent N+1 queries
     */
    protected function configureEagerLoading(): void
    {
        \App\Models\User::retrieved(function ($user) {
            if (!$user->relationLoaded('userDetail')) {
                $user->load('userDetail');
            }

            if (!$user->relationLoaded('conferencePermissions')) {
                $user->load('conferencePermissions');
            }
        });
    }
}
