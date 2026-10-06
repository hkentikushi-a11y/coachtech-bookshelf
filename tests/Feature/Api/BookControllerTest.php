<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── 公開エンドポイント（認証不要）────────────────────────────────────

    public function test_index_returns_paginated_books(): void
    {
        Book::factory()->count(3)->create();
        $this->getJson('/api/books')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links']);
    }

    public function test_show_returns_book(): void
    {
        $book = Book::factory()->create();
        $this->getJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.title', $book->title);
    }

    public function test_show_returns_404_for_invalid_id(): void
    {
        $this->getJson('/api/books/9999')->assertNotFound();
    }

    // ── 書き込み系：未認証は401 ───────────────────────────────────────────

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/books', ['title' => 'テスト'])
            ->assertUnauthorized();
    }

    public function test_update_requires_authentication(): void
    {
        $book = Book::factory()->create();
        $this->putJson("/api/books/{$book->id}", ['title' => '更新'])
            ->assertUnauthorized();
    }

    public function test_destroy_requires_authentication(): void
    {
        $book = Book::factory()->create();
        $this->deleteJson("/api/books/{$book->id}")
            ->assertUnauthorized();
    }

    // ── 書き込み系：Sanctum認証済み ──────────────────────────────────────

    public function test_store_creates_book_when_authenticated(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/books', [
            'title' => 'APIテスト書籍',
            'author' => 'APIテスト著者',
            'isbn' => '9784000000099',
            'genre_ids' => [$genre->id],
        ])->assertCreated()->assertJsonPath('data.title', 'APIテスト書籍');

        $this->assertDatabaseHas('books', [
            'isbn' => '9784000000099',
            'user_id' => $user->id,
        ]);
    }

    public function test_store_returns_422_with_invalid_data(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/books', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_owner_can_update_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $this->putJson("/api/books/{$book->id}", [
            'title' => '更新後タイトル',
            'author' => $book->author,
            'isbn' => $book->isbn,
        ])->assertOk()->assertJsonPath('data.title', '更新後タイトル');
    }

    public function test_non_owner_cannot_update_book(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        Sanctum::actingAs($other);

        $this->putJson("/api/books/{$book->id}", [
            'title' => '不正更新',
            'author' => $book->author,
            'isbn' => $book->isbn,
        ])->assertForbidden();
    }

    public function test_owner_can_delete_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $this->deleteJson("/api/books/{$book->id}")->assertNoContent();
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_non_owner_cannot_delete_book(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        Sanctum::actingAs($other);

        $this->deleteJson("/api/books/{$book->id}")->assertForbidden();
    }
}
