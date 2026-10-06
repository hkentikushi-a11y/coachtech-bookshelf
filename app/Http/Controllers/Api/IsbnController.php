<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoogleBooksService;
use Illuminate\Http\JsonResponse;

class IsbnController extends Controller
{
    public function __construct(private readonly GoogleBooksService $googleBooks) {}

    public function search(string $isbn): JsonResponse
    {
        $isbn = preg_replace('/[^0-9]/', '', $isbn);

        if (strlen($isbn) !== 13) {
            return response()->json(['message' => 'ISBNは13桁で指定してください。'], 422);
        }

        $book = $this->googleBooks->searchByIsbn($isbn);

        if ($book === null) {
            return response()->json(['message' => '書籍情報が見つかりませんでした。'], 404);
        }

        return response()->json(['data' => $book]);
    }
}
