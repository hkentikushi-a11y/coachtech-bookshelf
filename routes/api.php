<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\IsbnController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

// 公開エンドポイント（認証不要）
Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);
Route::get('isbn/{isbn}', [IsbnController::class, 'search']);

// トークン発行・失効
Route::post('tokens/create', [TokenController::class, 'create']);
Route::middleware('auth:sanctum')->delete('tokens/revoke', [TokenController::class, 'revoke']);

// 書き込み系（Sanctum トークン認証必須）
Route::middleware('auth:sanctum')->group(function () {
    Route::post('books', [BookController::class, 'store']);
    Route::put('books/{book}', [BookController::class, 'update']);
    Route::delete('books/{book}', [BookController::class, 'destroy']);
});
