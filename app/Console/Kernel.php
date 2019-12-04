<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected array $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('telescope:prune')->daily();
        $schedule->command('fetch:news')->everyFiveMinutes()->runInBackground();
        $schedule->command('analysis:news')->everyFiveMinutes()->runInBackground();
        $schedule->command('fetch:SteamUserCount')->everyTenMinutes()->runInBackground();
        $schedule->command('fetch:SteamWeeklyTopSellers')->everyTenMinutes()->runInBackground();
        $schedule->command('spider:SteamApps')->hourly()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
