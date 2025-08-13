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
    protected $commands = [
        // Commands\CheckOrderStatus::class,
        // Commands\CheckRepaymentStatus::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // default for withoutOverlapping is 1440 min (24h)
        // $schedule->command('add:borderou')->withoutOverlapping(5)->everyMinute();
        $schedule->command('add:borderou')->withoutOverlapping(5)->dailyAt('14:30');
        $schedule->command('check:orders')->withoutOverlapping(10)->everyFiveMinutes();
        $schedule->command('check:delivered-orders')->withoutOverlapping(5)->everyFiveMinutes();
        $schedule->command('check:borderou')->withoutOverlapping(5)->everyFiveMinutes();
        $schedule->command('add:invoice-sheet')->withoutOverlapping(5)->hourly();
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
