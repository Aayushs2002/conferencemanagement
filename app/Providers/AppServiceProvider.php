<?php

namespace App\Providers;

use App\Models\User\Country;
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\NamePrefix;
use Illuminate\Support\Facades\View;
use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            $countries = Country::whereStatus(1)->get();
            $view->with('countries', $countries);
        });

        View::composer('*', function ($view) {
            $name_prefiexs = NamePrefix::whereStatus(1)->get();
            $view->with('name_prefiexs', $name_prefiexs);
        });

        View::composer('*', function ($view) {
            $departments = Department::whereStatus(1)->get();
            $view->with('departments', $departments);
        });

        View::composer('*', function ($view) {
            $designations = Designation::whereStatus(1)->get();
            $view->with('designations', $designations);
        });

        View::composer('*', function ($view) {
            $institutions = Institution::whereStatus(1)->get();
            $view->with('institutions', $institutions);
        });
    }
}
