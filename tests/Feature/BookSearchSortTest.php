<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchSortTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_keyword_search_by_title(): void
    {
        Book::factory()->create(['user_id' => $this->user->id, 'title' => 'PHP入門', 'author' => '山田']);
        Book::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel実践', 'author' => '鈴木']);

        $this->get('/books?q=PHP')
            ->assertOk()
            ->assertSee('PHP入門')
            ->assertDontSee('Laravel実践');
    }

    public function test_keyword_search_by_author(): void
    {
        Book::factory()->create(['user_id' => $this->user->id, 'title' => 'PHP入門', 'author' => '山田太郎']);
        Book::factory()->create(['user_id' => $this->user->id, 'title' => 'Ruby入門', 'author' => '鈴木花子']);

        $this->get('/books?q=山田')
            ->assertOk()
            ->assertSee('PHP入門')
            ->assertDontSee('Ruby入門');
    }

    public function test_keyword_search_returns_empty_for_no_match(): void
    {
        Book::factory()->create(['user_id' => $this->user->id, 'title' => 'PHP入門', 'author' => '山田']);

        $response = $this->get('/books?q=Golang');
        $response->assertOk();

        $books = $response->viewData('books');
        $this->assertEquals(0, $books->total());
    }

    public function test_filter_by_genre(): void
    {
        $genreA = Genre::factory()->create(['name' => '技術']);
        $genreB = Genre::factory()->create(['name' => '小説']);

        $bookA = Book::factory()->create(['user_id' => $this->user->id, 'title' => '技術書']);
        $bookB = Book::factory()->create(['user_id' => $this->user->id, 'title' => '小説本']);
        $bookA->genres()->attach($genreA);
        $bookB->genres()->attach($genreB);

        $this->get("/books?genre_id={$genreA->id}")
            ->assertOk()
            ->assertSee('技術書')
            ->assertDontSee('小説本');
    }

    public function test_sort_by_title_alphabetically(): void
    {
        Book::factory()->create(['user_id' => $this->user->id, 'title' => 'Z タイトル']);
        Book::factory()->create(['user_id' => $this->user->id, 'title' => 'A タイトル']);

        $response = $this->get('/books?sort=title');
        $response->assertOk();

        $books = $response->viewData('books');
        $this->assertEquals('A タイトル', $books->first()->title);
    }

    public function test_sort_by_rating_highest_first(): void
    {
        $bookLow = Book::factory()->create(['user_id' => $this->user->id, 'title' => '低評価書']);
        $bookHigh = Book::factory()->create(['user_id' => $this->user->id, 'title' => '高評価書']);

        Review::factory()->create(['user_id' => $this->user->id, 'book_id' => $bookLow->id,  'rating' => 2]);
        Review::factory()->create(['user_id' => $this->user->id, 'book_id' => $bookHigh->id, 'rating' => 5]);

        $response = $this->get('/books?sort=rating');
        $response->assertOk();

        $books = $response->viewData('books');
        $this->assertEquals('高評価書', $books->first()->title);
    }

    public function test_pagination_preserves_query_string(): void
    {
        // 13 冊作成してページ 2 が生成されることを確認（1 ページ 12 件）
        Book::factory()->count(13)->create([
            'user_id' => $this->user->id,
            'title' => 'ページネーション書籍',
        ]);

        $response = $this->get('/books?q=ページネーション&sort=title');
        $response->assertOk();

        // ページネーションリンクにクエリ文字列が引き継がれること
        $response->assertSee('q=%E3%83%9A%E3%83%BC%E3%82%B8%E3%83%8D%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3', false);
    }
}
