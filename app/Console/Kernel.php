<?php

namespace App\Console;

use App\Console\Commands\ProcessReadingPlans;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // 毎朝 08:00 に読書計画の自動失効とリマインダー通知を実行
        $schedule->command(ProcessReadingPlans::class)->dailyAt('08:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
