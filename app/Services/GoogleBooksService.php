<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleBooksService
{
    private string $baseUrl;

    private ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.google_books.base_url', 'https://www.googleapis.com/books/v1');
        $this->apiKey = config('services.google_books.api_key');
    }

    /**
     * ISBNで書籍情報を検索し、見つかった場合は配列を返す。
     * 取得失敗・未ヒットの場合は null を返す。
     */
    public function searchByIsbn(string $isbn): ?array
    {
        try {
            $params = ['q' => "isbn:{$isbn}", 'maxResults' => 1];

            if ($this->apiKey) {
                $params['key'] = $this->apiKey;
            }

            $response = Http::timeout(5)->get("{$this->baseUrl}/volumes", $params);

            if (! $response->ok()) {
                return null;
            }

            $data = $response->json();

            if (($data['totalItems'] ?? 0) === 0) {
                return null;
            }

            $info = $data['items'][0]['volumeInfo'] ?? [];

            return [
                'title' => $info['title'] ?? null,
                'author' => implode(', ', $info['authors'] ?? []) ?: null,
                'published_date' => $this->normalizeDate($info['publishedDate'] ?? null),
                'description' => $info['description'] ?? null,
                'image_url' => $info['imageLinks']['thumbnail'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::warning("GoogleBooksService: {$e->getMessage()}");

            return null;
        }
    }

    private function normalizeDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        // "2012-06" や "2012" → "2012-06-01" / "2012-01-01" に正規化
        if (preg_match('/^\d{4}$/', $date)) {
            return "{$date}-01-01";
        }

        if (preg_match('/^\d{4}-\d{2}$/', $date)) {
            return "{$date}-01";
        }

        return $date;
    }
}
