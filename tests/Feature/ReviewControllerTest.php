<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $this->actingAs($user)
            ->post(route('reviews.store', $book), ['rating' => 5, 'comment' => '素晴らしい本です'])
            ->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'book_id' => $book->id, 'rating' => 5]);
    }

    public function test_store_requires_auth(): void
    {
        $book = Book::factory()->create();
        $this->post(route('reviews.store', $book), ['rating' => 5, 'comment' => 'テスト'])
            ->assertRedirect(route('login'));
    }

    public function test_store_prevents_duplicate_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        $this->actingAs($user)
            ->post(route('reviews.store', $book), ['rating' => 3, 'comment' => '二重投稿'])
            ->assertSessionHasErrors(['rating']);
    }

    public function test_store_validates_rating_range(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $this->actingAs($user)
            ->post(route('reviews.store', $book), ['rating' => 6, 'comment' => 'テスト'])
            ->assertSessionHasErrors(['rating']);
    }

    public function test_owner_can_edit_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        $this->actingAs($user)->get(route('reviews.edit', [$book, $review]))->assertOk();
    }

    public function test_non_owner_cannot_edit_review(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);
        $this->actingAs($other)->get(route('reviews.edit', [$book, $review]))->assertForbidden();
    }

    public function test_owner_can_delete_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        $this->actingAs($user)
            ->delete(route('reviews.destroy', [$book, $review]))
            ->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_non_owner_cannot_delete_review(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);
        $this->actingAs($other)
            ->delete(route('reviews.destroy', [$book, $review]))
            ->assertForbidden();
    }

    public function test_owner_can_update_review(): void
    {
        $owner = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        Review::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id, 'rating' => 3, 'comment' => '旧コメント']);
        $review = Review::where('user_id', $owner->id)->first();

        $this->actingAs($owner)->put(route('reviews.update', [$book, $review]), [
            'rating' => 5,
            'comment' => '新コメント',
        ])->assertRedirect();

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'rating' => 5]);
    }

    public function test_non_owner_cannot_update_review(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        $review = Review::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);

        $this->actingAs($other)->put(route('reviews.update', [$book, $review]), [
            'rating' => 1,
            'comment' => '改ざん',
        ])->assertForbidden();
    }
}
