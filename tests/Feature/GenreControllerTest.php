<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_genres(): void
    {
        Genre::factory()->count(3)->create();
        $this->get(route('genres.index'))->assertOk()->assertViewIs('genres.index');
    }

    public function test_show_displays_books_for_genre(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $book->genres()->attach($genre->id);
        $this->get(route('genres.show', $genre))->assertOk()->assertViewIs('genres.show');
    }

    public function test_create_requires_auth(): void
    {
        $this->get(route('genres.create'))->assertRedirect(route('login'));
    }

    public function test_store_creates_genre(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('genres.store'), ['name' => '新ジャンル'])
            ->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', ['name' => '新ジャンル']);
    }

    public function test_store_validates_unique_name(): void
    {
        $user = User::factory()->create();
        Genre::factory()->create(['name' => '重複ジャンル']);
        $this->actingAs($user)
            ->post(route('genres.store'), ['name' => '重複ジャンル'])
            ->assertSessionHasErrors(['name']);
    }

    public function test_update_changes_genre_name(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '旧名前']);
        $this->actingAs($user)
            ->put(route('genres.update', $genre), ['name' => '新名前'])
            ->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', ['name' => '新名前']);
    }

    public function test_destroy_fails_when_books_are_attached(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $book->genres()->attach($genre->id);
        $this->actingAs($user)
            ->delete(route('genres.destroy', $genre))
            ->assertRedirect();
        $this->assertDatabaseHas('genres', ['id' => $genre->id]);
    }

    public function test_destroy_succeeds_without_books(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $this->actingAs($user)
            ->delete(route('genres.destroy', $genre))
            ->assertRedirect(route('genres.index'));
        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }
}
