<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            \App\Http\Controllers\DebtController::sendAutoReminders();
        })->dailyAt('09:00');
    }

    /**
     * Register the application's command schedule.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
