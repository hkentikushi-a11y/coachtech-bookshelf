<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ranking_page_is_accessible(): void
    {
        $this->get(route('ranking.index'))->assertOk()->assertViewIs('ranking.index');
    }

    public function test_ranking_excludes_books_without_reviews(): void
    {
        $bookWithReview = Book::factory()->create();
        $bookWithoutReview = Book::factory()->create();
        Review::factory()->create(['book_id' => $bookWithReview->id, 'rating' => 5]);

        $response = $this->get(route('ranking.index'));
        $books = $response->viewData('books');

        $ids = $books->pluck('id')->toArray();
        $this->assertContains($bookWithReview->id, $ids);
        $this->assertNotContains($bookWithoutReview->id, $ids);
    }

    public function test_ranking_limits_to_10_books(): void
    {
        $books = Book::factory()->count(12)->create();
        foreach ($books as $book) {
            Review::factory()->create(['book_id' => $book->id, 'rating' => rand(1, 5)]);
        }
        $response = $this->get(route('ranking.index'));
        $this->assertCount(10, $response->viewData('books'));
    }

    public function test_ranking_orders_by_average_rating_desc(): void
    {
        $highBook = Book::factory()->create();
        $lowBook = Book::factory()->create();
        Review::factory()->create(['book_id' => $highBook->id, 'rating' => 5]);
        Review::factory()->create(['book_id' => $lowBook->id, 'rating' => 1]);

        $response = $this->get(route('ranking.index'));
        $books = $response->viewData('books');
        $this->assertEquals($highBook->id, $books->first()->id);
    }
}
