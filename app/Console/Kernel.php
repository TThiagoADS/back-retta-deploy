<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Application\Jobs\FetchDeputiesExpensesJob;
use App\Application\Jobs\FetchDeputyExpensesJob;
use App\Application\Jobs\FetchAllDeputiesExpensesJob;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\FetchDeputiesCommand::class,
        \App\Console\Commands\FetchExpensesCommand::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new FetchDeputiesExpensesJob())
                ->daily()
                ->at('02:00')
                ->name('fetch-all-deputies-expenses')
                ->withoutOverlapping()
                ->runInBackground();

        $schedule->job(new FetchDeputiesJob())
                ->weekly()
                ->sundays()
                ->at('01:00')
                ->name('fetch-deputies')
                ->withoutOverlapping()
                ->runInBackground();
        $schedule->job(new FetchAllDeputiesExpensesJob())
                ->daily()
                ->at('02:30')
                ->name('fetch-all-deputies-expenses')
                ->withoutOverlapping()
                ->runInBackground();

        $schedule->command('queue:prune-batches --hours=48')
                ->daily()
                ->at('03:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
