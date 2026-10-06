<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_reading_auto_sets_started_at(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('reading-plans.store'), [
            'book_id' => $book->id,
            'status' => 'reading',
        ]);

        $plan = ReadingPlan::where('user_id', $user->id)->first();
        $this->assertNotNull($plan->started_at);
    }

    public function test_update_to_done_auto_sets_finished_at(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'reading',
        ]);

        $this->actingAs($user)->put(route('reading-plans.update', $plan), [
            'status' => 'done',
        ]);

        $this->assertNotNull($plan->fresh()->finished_at);
    }

    public function test_other_user_cannot_delete_plan(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        $plan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($other)
            ->delete(route('reading-plans.destroy', $plan))
            ->assertForbidden();

        $this->assertDatabaseHas('reading_plans', ['id' => $plan->id]);
    }

    public function test_other_user_cannot_mark_done(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        $plan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
            'status' => 'reading',
        ]);

        $this->actingAs($other)
            ->post(route('reading-plans.done', $plan))
            ->assertForbidden();
    }

    public function test_cannot_register_same_book_twice(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // 同一ユーザーが同じ書籍を再登録しようとした場合、create ページには表示されない
        $response = $this->actingAs($user)->get(route('reading-plans.create'));
        $response->assertOk();

        $books = $response->viewData('books');
        $this->assertFalse($books->contains('id', $book->id));
    }

    public function test_validation_fails_for_missing_status(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('reading-plans.store'), [
            'book_id' => $book->id,
        ])->assertSessionHasErrors(['status']);
    }
}
