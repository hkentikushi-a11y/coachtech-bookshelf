<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_books(): void
    {
        $user = User::factory()->create();
        Book::factory()->count(2)->create(['user_id' => $user->id]);
        $this->assertCount(2, $user->books);
    }

    public function test_user_has_many_reviews(): void
    {
        $user = User::factory()->create();
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book1->id]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book2->id]);
        $this->assertCount(2, $user->reviews);
    }

    public function test_user_favorites_books(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favorites()->attach($book->id);
        $this->assertTrue($user->favorites->contains($book));
    }

    public function test_user_likes_reviews(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id]);
        $user->reviewLikes()->attach($review->id);
        $this->assertTrue($user->reviewLikes->contains($review));
    }
}
