<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_belongs_to_user(): void
    {
        $book = Book::factory()->create();
        $this->assertInstanceOf(User::class, $book->user);
    }

    public function test_book_has_many_reviews(): void
    {
        $book = Book::factory()->create();
        Review::factory()->count(3)->create(['book_id' => $book->id]);
        $this->assertCount(3, $book->reviews);
    }

    public function test_book_belongs_to_many_genres(): void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();
        $book->genres()->attach($genre->id);
        $this->assertTrue($book->genres->contains($genre));
    }

    public function test_average_rating_returns_null_with_no_reviews(): void
    {
        $book = Book::factory()->create();
        $this->assertNull($book->averageRating());
    }

    public function test_average_rating_calculates_correctly(): void
    {
        $book = Book::factory()->create();
        Review::factory()->create(['book_id' => $book->id, 'rating' => 4]);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 2]);
        $this->assertEquals(3.0, $book->fresh()->averageRating());
    }
}
