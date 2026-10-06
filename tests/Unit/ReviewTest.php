<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_belongs_to_user(): void
    {
        $review = Review::factory()->create();
        $this->assertInstanceOf(User::class, $review->user);
    }

    public function test_review_belongs_to_book(): void
    {
        $review = Review::factory()->create();
        $this->assertInstanceOf(Book::class, $review->book);
    }

    public function test_review_has_likes(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id]);
        $liker = User::factory()->create();
        $review->likes()->attach($liker->id);
        $this->assertTrue($review->likes->contains($liker));
    }
}
