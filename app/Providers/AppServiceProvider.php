<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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
        View::composer('layouts.partials.topbar', function ($view) {
            if (Auth::check()) {
                $unreadNotifications = Auth::user()->unreadNotifications;
                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
        
    }
}
