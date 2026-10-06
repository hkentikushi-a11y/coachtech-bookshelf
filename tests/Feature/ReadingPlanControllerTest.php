<?php

namespace Tests\Feature;

use App\Enums\ReadingStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── 認証ガード ────────────────────────────────────────────────────────────
    public function test_guest_cannot_access_reading_plans(): void
    {
        $this->get(route('reading-plans.index'))->assertRedirect('/login');
    }

    // ── 一覧 ──────────────────────────────────────────────────────────────────
    public function test_authenticated_user_can_view_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('reading-plans.index'))
            ->assertOk()
            ->assertViewIs('reading-plans.index');
    }

    // ── 登録 ──────────────────────────────────────────────────────────────────
    public function test_user_can_create_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('reading-plans.store'), [
            'book_id' => $book->id,
            'status' => 'want',
            'target_date' => now()->addDays(30)->toDateString(),
        ]);

        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'want',
        ]);
    }

    // ── 読了アクション ────────────────────────────────────────────────────────
    public function test_user_can_mark_plan_as_done(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'reading',
        ]);

        $this->actingAs($user)
            ->post(route('reading-plans.done', $plan))
            ->assertRedirect(route('reading-plans.index'));

        $this->assertEquals(ReadingStatus::Done, $plan->fresh()->status);
        $this->assertNotNull($plan->fresh()->finished_at);
    }

    // ── 他人の計画は操作不可 ──────────────────────────────────────────────────
    public function test_other_user_cannot_update_reading_plan(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        $plan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($other)
            ->put(route('reading-plans.update', $plan), ['status' => 'done'])
            ->assertForbidden();
    }

    // ── 削除 ──────────────────────────────────────────────────────────────────
    public function test_user_can_delete_own_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->delete(route('reading-plans.destroy', $plan))
            ->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseMissing('reading_plans', ['id' => $plan->id]);
    }
}
