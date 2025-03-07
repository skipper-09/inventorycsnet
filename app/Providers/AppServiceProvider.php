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
                if (Auth::user()->hasRole('Developer')) {
                    $unreadNotifications = Auth::user()->unreadNotifications()->whereNull('read_at')->get();
                } else {
                    $unreadNotifications = Auth::user()->unreadNotifications()
                    ->whereNull('read_at')
                    ->orderByDesc('created_at')
                    ->take(10)
                    ->get();
                }
                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
        
    }
}
