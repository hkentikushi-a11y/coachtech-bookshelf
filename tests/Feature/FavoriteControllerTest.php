<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_adds_favorite(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $this->actingAs($user)->post(route('favorites.toggle', $book));
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    public function test_toggle_removes_existing_favorite(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favorites()->attach($book->id);
        $this->actingAs($user)->post(route('favorites.toggle', $book));
        $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    public function test_toggle_requires_auth(): void
    {
        $book = Book::factory()->create();
        $this->post(route('favorites.toggle', $book))->assertRedirect(route('login'));
    }

    public function test_index_shows_favorited_books(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favorites()->attach($book->id);
        $this->actingAs($user)->get(route('favorites.index'))->assertOk()->assertViewIs('favorites.index');
    }
}
