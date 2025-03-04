<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class KernelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Schedule $schedule): void
    {
            // Update status to overdue every 12 AM
            $schedule->command('task:update-employeetask-status')->dailyAt('00:00');
    }
}
