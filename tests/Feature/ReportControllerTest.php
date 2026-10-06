<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('report.index'))->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_report(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('report.index'))
            ->assertOk()
            ->assertViewIs('reports.index')
            ->assertSeeText('マイ読書レポート');
    }

    public function test_report_shows_correct_summary(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '技術']);

        $books = Book::factory()->count(3)->create(['user_id' => $user->id]);
        foreach ($books as $book) {
            $book->genres()->attach($genre);
        }

        // ユーザー自身のレビュー（評価 4, 5, 3）
        Review::factory()->create(['user_id' => $user->id,  'book_id' => $books[0]->id, 'rating' => 4]);
        Review::factory()->create(['user_id' => $user->id,  'book_id' => $books[1]->id, 'rating' => 5]);
        Review::factory()->create(['user_id' => $user->id,  'book_id' => $books[2]->id, 'rating' => 3]);
        // 他ユーザーのレビューは集計されないこと
        Review::factory()->create(['user_id' => $other->id, 'book_id' => $books[0]->id, 'rating' => 1]);

        $response = $this->actingAs($user)->get(route('report.index'));

        $response->assertOk()
            ->assertViewHas('totalReviews', 3)
            ->assertViewHas('booksReviewed', 3)
            ->assertViewHas('avgRating', 4.0);
    }

    public function test_rating_distribution_covers_all_stars(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book->id, 'rating' => 5]);

        $response = $this->actingAs($user)->get(route('report.index'));

        $dist = $response->viewData('ratingDistribution');

        // 1〜5 の全キーが存在すること
        $this->assertCount(5, $dist);
        $this->assertEquals(1, $dist[5]);
        $this->assertEquals(0, $dist[1]);
    }

    public function test_top_books_are_ordered_by_rating_desc(): void
    {
        $user = User::factory()->create();
        $bookA = Book::factory()->create(['user_id' => $user->id, 'title' => '低評価本']);
        $bookB = Book::factory()->create(['user_id' => $user->id, 'title' => '高評価本']);

        Review::factory()->create(['user_id' => $user->id, 'book_id' => $bookA->id, 'rating' => 2]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $bookB->id, 'rating' => 5]);

        $response = $this->actingAs($user)->get(route('report.index'));

        $top = $response->viewData('topBooks');
        $this->assertEquals('高評価本', $top->first()->book->title);
    }

    public function test_genre_ratings_are_ordered_by_avg_desc(): void
    {
        $user = User::factory()->create();
        $genreA = Genre::factory()->create(['name' => '低評価ジャンル']);
        $genreB = Genre::factory()->create(['name' => '高評価ジャンル']);

        $bookA = Book::factory()->create(['user_id' => $user->id]);
        $bookB = Book::factory()->create(['user_id' => $user->id]);
        $bookA->genres()->attach($genreA);
        $bookB->genres()->attach($genreB);

        Review::factory()->create(['user_id' => $user->id, 'book_id' => $bookA->id, 'rating' => 2]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $bookB->id, 'rating' => 5]);

        $response = $this->actingAs($user)->get(route('report.index'));

        $genres = $response->viewData('genreRatings');
        $this->assertEquals('高評価ジャンル', $genres->first()->name);
    }

    public function test_report_shows_zero_values_when_no_reviews(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('report.index'));

        $response->assertOk()
            ->assertViewHas('totalReviews', 0)
            ->assertViewHas('avgRating', null)
            ->assertViewHas('booksReviewed', 0);
    }

    public function test_report_only_shows_own_reviews(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $other->id]);

        // 他ユーザーのレビューのみ存在する場合、自分のレポートはゼロになる
        Review::factory()->create(['user_id' => $other->id, 'book_id' => $book->id, 'rating' => 5]);

        $response = $this->actingAs($user)->get(route('report.index'));

        $response->assertViewHas('totalReviews', 0);
    }

    public function test_rating_distribution_is_5_to_1_descending(): void
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(3)->create(['user_id' => $user->id]);

        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[0]->id, 'rating' => 5]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[1]->id, 'rating' => 3]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $books[2]->id, 'rating' => 1]);

        $response = $this->actingAs($user)->get(route('report.index'));
        $dist = $response->viewData('ratingDistribution');

        // キーが 5 から 1 の降順になっていること
        $this->assertEquals([5, 4, 3, 2, 1], $dist->keys()->toArray());
        $this->assertEquals(1, $dist[5]);
        $this->assertEquals(0, $dist[4]);
        $this->assertEquals(1, $dist[3]);
    }
}
