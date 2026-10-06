<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessReadingPlansTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_reading_plans_are_expired(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'reading',
            'target_date' => now()->subDays(3)->toDateString(),
        ]);

        $this->artisan('reading-plans:process')->assertSuccessful();

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'status' => 'expired',
        ]);
    }

    public function test_reminder_notification_is_sent_for_due_soon_plans(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'reading',
            'target_date' => now()->addDays(7)->toDateString(),
        ]);

        $this->artisan('reading-plans:process')->assertSuccessful();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => ReadingReminder::class,
        ]);
    }

    public function test_reminder_sent_for_3_day_plans(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'reading',
            'target_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->artisan('reading-plans:process')->assertSuccessful();

        $this->assertDatabaseHas('notifications', ['user_id' => $user->id]);
    }

    public function test_reminder_sent_for_1_day_plans(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'reading',
            'target_date' => now()->addDays(1)->toDateString(),
        ]);

        $this->artisan('reading-plans:process')->assertSuccessful();

        $this->assertDatabaseHas('notifications', ['user_id' => $user->id]);
    }

    public function test_no_reminder_for_want_status_plans(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'want',
            'target_date' => now()->addDays(1)->toDateString(),
        ]);

        $this->artisan('reading-plans:process')->assertSuccessful();

        $this->assertDatabaseMissing('notifications', ['user_id' => $user->id]);
    }

    public function test_done_plans_are_not_expired(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'done',
            'target_date' => now()->subDays(5)->toDateString(),
        ]);

        $this->artisan('reading-plans:process')->assertSuccessful();

        $this->assertDatabaseMissing('reading_plans', [
            'user_id' => $user->id,
            'status' => 'expired',
        ]);
    }
}
