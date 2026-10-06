<?php

namespace App\Console\Commands;

use App\Models\ReadingPlan;
use App\Notifications\ReadingReminder;
use Illuminate\Console\Command;

class ProcessReadingPlans extends Command
{
    protected $signature = 'reading-plans:process';

    protected $description = '期限切れ読書計画の自動失効とリマインダー通知を送信する';

    public function handle(): int
    {
        $today = now()->toDateString();

        // ── 1. 自動失効（status=reading かつ target_date < today） ──────────
        $expiredCount = ReadingPlan::where('status', 'reading')
            ->whereNotNull('target_date')
            ->where('target_date', '<', $today)
            ->update(['status' => 'expired']);

        $this->info("期限切れ計画を {$expiredCount} 件失効させました。");

        // ── 2. リマインダー通知（7日前・3日前・1日前） ──────────────────────
        foreach ([7, 3, 1] as $days) {
            $plans = ReadingPlan::with(['book', 'user'])
                ->where('status', 'reading')
                ->whereNotNull('target_date')
                ->whereDate('target_date', now()->addDays($days)->toDateString())
                ->get();

            foreach ($plans as $plan) {
                (new ReadingReminder(
                    $plan,
                    "「{$plan->book->title}」の目標日まであと {$days} 日です。"
                ))->send();
            }

            $this->info("{$days} 日前リマインダーを {$plans->count()} 件送信しました。");
        }

        return self::SUCCESS;
    }
}
