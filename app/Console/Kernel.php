<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\MigrateImages::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        //
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        // Load additional commands if necessary
        $this->load(__DIR__ . '/Commands');
    }
}
