<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_books(): void
    {
        Book::factory()->count(3)->create();
        $response = $this->get(route('books.index'));
        $response->assertOk();
        $response->assertViewIs('books.index');
    }

    public function test_show_displays_book(): void
    {
        $book = Book::factory()->create();
        $response = $this->get(route('books.show', $book));
        $response->assertOk();
        $response->assertViewIs('books.show');
    }

    public function test_create_requires_auth(): void
    {
        $this->get(route('books.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_create(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('books.create'))->assertOk();
    }

    public function test_store_creates_book_with_genres(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '9784000000001',
            'genre_ids' => [$genre->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', ['isbn' => '9784000000001']);
        $this->assertDatabaseHas('book_genre', ['genre_id' => $genre->id]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('books.store'), [])
            ->assertSessionHasErrors(['title', 'author', 'isbn', 'genre_ids']);
    }

    public function test_store_validates_isbn_length(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $this->actingAs($user)
            ->post(route('books.store'), [
                'title' => 'テスト', 'author' => '著者',
                'isbn' => '123', 'genre_ids' => [$genre->id],
            ])
            ->assertSessionHasErrors(['isbn']);
    }

    public function test_owner_can_edit_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('books.edit', $book))->assertOk();
    }

    public function test_non_owner_cannot_edit_book(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        $this->actingAs($other)->get(route('books.edit', $book))->assertForbidden();
    }

    public function test_owner_can_delete_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->delete(route('books.destroy', $book))->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_non_owner_cannot_delete_book(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);
        $this->actingAs($other)->delete(route('books.destroy', $book))->assertForbidden();
    }

    public function test_owner_can_update_book(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id, 'title' => '旧タイトル']);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => '新タイトル',
            'author' => '著者名',
            'isbn' => '9784101010014',
            'genre_ids' => [$genre->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => '新タイトル']);
    }

    public function test_non_owner_cannot_update_book(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id, 'title' => '元タイトル']);

        $this->actingAs($other)->put(route('books.update', $book), [
            'title' => '変更タイトル',
            'author' => '著者名',
            'isbn' => '9784101010014',
            'genre_ids' => [$genre->id],
        ])->assertForbidden();
    }
}
