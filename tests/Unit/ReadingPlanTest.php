<?php

namespace Tests\Unit;

use App\Enums\ReadingStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanTest extends TestCase
{
    use RefreshDatabase;

    private function makePlan(array $attrs = []): ReadingPlan
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        return ReadingPlan::factory()->create(array_merge([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ], $attrs));
    }

    public function test_active_scope_includes_want_and_reading(): void
    {
        $this->makePlan(['status' => 'want']);
        $this->makePlan(['status' => 'reading']);
        $this->makePlan(['status' => 'done']);

        $this->assertCount(2, ReadingPlan::active()->get());
    }

    public function test_overdue_scope_returns_reading_past_target_date(): void
    {
        $this->makePlan([
            'status' => 'reading',
            'target_date' => now()->subDays(1)->toDateString(),
        ]);
        $this->makePlan([
            'status' => 'reading',
            'target_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->assertCount(1, ReadingPlan::overdue()->get());
    }

    public function test_due_soon_scope_returns_plans_on_exact_day(): void
    {
        $this->makePlan([
            'status' => 'reading',
            'target_date' => now()->addDays(3)->toDateString(),
        ]);
        $this->makePlan([
            'status' => 'reading',
            'target_date' => now()->addDays(7)->toDateString(),
        ]);

        $this->assertCount(1, ReadingPlan::dueSoon(3)->get());
    }

    public function test_status_cast_returns_enum(): void
    {
        $plan = $this->makePlan(['status' => 'reading']);
        $this->assertInstanceOf(ReadingStatus::class, $plan->status);
        $this->assertEquals(ReadingStatus::Reading, $plan->status);
    }

    public function test_reading_status_label(): void
    {
        $this->assertEquals('読みたい', ReadingStatus::Want->label());
        $this->assertEquals('読んでいる', ReadingStatus::Reading->label());
        $this->assertEquals('読了', ReadingStatus::Done->label());
        $this->assertEquals('期限切れ', ReadingStatus::Expired->label());
    }
}
