<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Policies\ReadingPlanPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_edit_page(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $plan = ReadingPlan::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

        $this->actingAs($user)
            ->get(route('reading-plans.edit', $plan))
            ->assertOk();
    }

    public function test_other_user_cannot_view_edit_page(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        $plan = ReadingPlan::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);

        $this->actingAs($other)
            ->get(route('reading-plans.edit', $plan))
            ->assertForbidden();
    }

    public function test_authenticated_user_can_access_create_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('reading-plans.create'))
            ->assertOk();
    }

    public function test_all_policy_methods_directly(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        $plan = ReadingPlan::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);

        $policy = new ReadingPlanPolicy;

        // viewAny: 認証済みなら誰でも true
        $this->assertTrue($policy->viewAny($owner));
        $this->assertTrue($policy->viewAny($other));

        // view: 所有者のみ true
        $this->assertTrue($policy->view($owner, $plan));
        $this->assertFalse($policy->view($other, $plan));

        // create: 認証済みなら誰でも true
        $this->assertTrue($policy->create($owner));

        // update/delete/markDone: 所有者のみ true
        $this->assertTrue($policy->update($owner, $plan));
        $this->assertFalse($policy->update($other, $plan));

        $this->assertTrue($policy->delete($owner, $plan));
        $this->assertFalse($policy->delete($other, $plan));

        $this->assertTrue($policy->markDone($owner, $plan));
        $this->assertFalse($policy->markDone($other, $plan));
    }
}
