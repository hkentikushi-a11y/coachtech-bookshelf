<?php

namespace App\Notifications;

use App\Models\Notification;
use App\Models\ReadingPlan;

class ReadingReminder
{
    public function __construct(
        private ReadingPlan $plan,
        private string $message
    ) {}

    public function toDatabase(): array
    {
        return [
            'plan_id' => $this->plan->id,
            'book_id' => $this->plan->book_id,
            'book_title' => $this->plan->book->title,
            'message' => $this->message,
            'target_date' => $this->plan->target_date?->toDateString(),
        ];
    }

    public function send(): void
    {
        Notification::create([
            'user_id' => $this->plan->user_id,
            'type' => static::class,
            'data' => $this->toDatabase(),
        ]);
    }
}
