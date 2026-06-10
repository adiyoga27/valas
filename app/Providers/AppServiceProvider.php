<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use App\Models\Office;
use Spatie\Activitylog\Models\Activity;

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
        \Illuminate\Pagination\Paginator::defaultView('pagination.tailwind');

        View::composer('layouts.admin', function ($view) {
            $view->with('office', Office::first());
        });

        Activity::saving(function (Activity $activity) {
        $activity->properties = $activity->properties->merge([
            'ip'  => Request::ip(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
        ]);
    });
    }
}
