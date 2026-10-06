<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::with(['genres', 'user', 'reviews'])->latest();

        if ($q = $request->input('q')) {
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%");
            });
        }

        if ($genreId = $request->input('genre_id')) {
            $query->whereHas('genres', fn ($b) => $b->where('genres.id', $genreId));
        }

        match ($request->input('sort', 'latest')) {
            'rating' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            'title' => $query->reorder()->orderBy('title'),
            default => $query,
        };

        $books = $query->paginate(12)->withQueryString();
        $genres = Genre::orderBy('name')->get();

        $favoritedIds = auth()->check()
            ? auth()->user()->favorites()->pluck('books.id')->toArray()
            : [];

        return view('books.index', compact('books', 'genres', 'favoritedIds'));
    }

    public function show(Book $book): View
    {
        $book->load(['genres', 'user', 'reviews.user', 'reviews.likes']);

        $isFavorited = auth()->check()
            && $book->favoritedBy()->where('user_id', auth()->id())->exists();

        $likedReviewIds = auth()->check()
            ? auth()->user()->reviewLikes()->pluck('reviews.id')->toArray()
            : [];

        return view('books.show', compact('book', 'isFavorited', 'likedReviewIds'));
    }

    public function create(): View
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    public function store(BookStoreRequest $request): RedirectResponse
    {
        $book = Book::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_date' => $request->published_date,
            'description' => $request->description,
            'image_url' => $request->image_url,
        ]);

        $book->genres()->sync($request->genre_ids);

        return redirect()->route('books.show', $book)
            ->with('success', '書籍を登録しました');
    }

    public function edit(Book $book): View
    {
        $this->authorize('update', $book);
        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    public function update(BookUpdateRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_date' => $request->published_date,
            'description' => $request->description,
            'image_url' => $request->image_url,
        ]);

        $book->genres()->sync($request->genre_ids);

        return redirect()->route('books.show', $book)
            ->with('success', '書籍を更新しました');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', '書籍を削除しました');
    }
}
