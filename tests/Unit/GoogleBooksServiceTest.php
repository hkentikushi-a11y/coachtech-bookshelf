<?php

namespace Tests\Unit;

use App\Services\GoogleBooksService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleBooksServiceTest extends TestCase
{
    private function makeResponse(array $overrides = []): array
    {
        return array_merge([
            'totalItems' => 1,
            'items' => [[
                'volumeInfo' => array_merge([
                    'title' => 'テスト書籍',
                    'authors' => ['著者 A', '著者 B'],
                    'publishedDate' => '2024-03-15',
                    'description' => 'テスト説明',
                    'imageLinks' => ['thumbnail' => 'https://example.com/cover.jpg'],
                ], $overrides['volumeInfo'] ?? []),
            ]],
        ], $overrides);
    }

    public function test_search_by_isbn_returns_book_data(): void
    {
        Http::fake([
            '*' => Http::response($this->makeResponse(), 200),
        ]);

        $service = new GoogleBooksService;
        $result = $service->searchByIsbn('9784101010014');

        $this->assertNotNull($result);
        $this->assertEquals('テスト書籍', $result['title']);
        $this->assertEquals('著者 A, 著者 B', $result['author']);
        $this->assertEquals('2024-03-15', $result['published_date']);
        $this->assertEquals('https://example.com/cover.jpg', $result['image_url']);
    }

    public function test_year_only_date_is_normalized(): void
    {
        Http::fake([
            '*' => Http::response($this->makeResponse(['volumeInfo' => ['publishedDate' => '2020']]), 200),
        ]);

        $result = (new GoogleBooksService)->searchByIsbn('9784101010014');

        $this->assertEquals('2020-01-01', $result['published_date']);
    }

    public function test_year_month_date_is_normalized(): void
    {
        Http::fake([
            '*' => Http::response($this->makeResponse(['volumeInfo' => ['publishedDate' => '2020-07']]), 200),
        ]);

        $result = (new GoogleBooksService)->searchByIsbn('9784101010014');

        $this->assertEquals('2020-07-01', $result['published_date']);
    }

    public function test_returns_null_when_no_items(): void
    {
        Http::fake([
            '*' => Http::response(['totalItems' => 0, 'items' => []], 200),
        ]);

        $result = (new GoogleBooksService)->searchByIsbn('9780000000000');

        $this->assertNull($result);
    }

    public function test_returns_null_on_http_error(): void
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $result = (new GoogleBooksService)->searchByIsbn('9784101010014');

        $this->assertNull($result);
    }

    public function test_returns_null_on_connection_failure(): void
    {
        // 接続失敗をシミュレート（Guzzle の ConnectionException）
        Http::fake(function () {
            throw new ConnectionException('Connection refused');
        });

        $result = (new GoogleBooksService)->searchByIsbn('9784101010014');

        $this->assertNull($result);
    }

    public function test_multiple_authors_are_joined_with_comma(): void
    {
        Http::fake([
            '*' => Http::response($this->makeResponse(['volumeInfo' => [
                'title' => 'Test',
                'authors' => ['著者 X', '著者 Y', '著者 Z'],
            ]]), 200),
        ]);

        $result = (new GoogleBooksService)->searchByIsbn('9784101010014');

        $this->assertEquals('著者 X, 著者 Y, 著者 Z', $result['author']);
    }
}
