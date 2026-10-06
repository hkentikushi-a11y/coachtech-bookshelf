<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_cannot_access_books_create(): void
    {
        $this->get(route('books.create'))->assertRedirect(route('login'));
    }

    public function test_unauthenticated_cannot_post_books(): void
    {
        $this->post(route('books.store'), [])->assertRedirect(route('login'));
    }

    public function test_unauthenticated_cannot_access_genres_create(): void
    {
        $this->get(route('genres.create'))->assertRedirect(route('login'));
    }

    public function test_unauthenticated_cannot_access_favorites(): void
    {
        $this->get(route('favorites.index'))->assertRedirect(route('login'));
    }

    public function test_unauthenticated_cannot_post_review(): void
    {
        $book = Book::factory()->create();
        $this->post(route('reviews.store', $book), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('books.create'))->assertOk();
        $this->actingAs($user)->get(route('genres.create'))->assertOk();
        $this->actingAs($user)->get(route('favorites.index'))->assertOk();
    }

    public function test_public_routes_accessible_without_auth(): void
    {
        $this->get(route('books.index'))->assertOk();
        $this->get(route('ranking.index'))->assertOk();
        $this->get(route('genres.index'))->assertOk();
    }
}
