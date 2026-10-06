<?php

namespace Tests\Feature\Api;

use App\Services\GoogleBooksService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IsbnControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_isbn_returns_book_data(): void
    {
        // GoogleBooksService をモックして外部HTTP呼び出しを回避
        $this->mock(GoogleBooksService::class, function ($mock) {
            $mock->shouldReceive('searchByIsbn')
                ->with('9784101010014')
                ->once()
                ->andReturn([
                    'title' => '坊っちゃん',
                    'author' => '夏目漱石',
                    'published_date' => '1906-01-01',
                    'description' => 'テスト説明',
                    'image_url' => 'https://example.com/cover.jpg',
                ]);
        });

        $response = $this->getJson('/api/isbn/9784101010014');

        $response->assertOk()
            ->assertJsonPath('data.title', '坊っちゃん')
            ->assertJsonPath('data.author', '夏目漱石');
    }

    public function test_not_found_isbn_returns_404(): void
    {
        $this->mock(GoogleBooksService::class, function ($mock) {
            $mock->shouldReceive('searchByIsbn')
                ->once()
                ->andReturn(null);
        });

        $response = $this->getJson('/api/isbn/9780000000000');

        $response->assertNotFound();
    }

    public function test_invalid_isbn_length_returns_422(): void
    {
        $response = $this->getJson('/api/isbn/123');

        $response->assertStatus(422);
    }
}
