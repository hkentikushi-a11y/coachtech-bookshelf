<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewStoreRequest;
use App\Http\Requests\ReviewUpdateRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(ReviewStoreRequest $request, Book $book): RedirectResponse
    {
        $book->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('books.show', $book)
            ->with('success', 'レビューを投稿しました');
    }

    public function edit(Book $book, Review $review): View
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('book', 'review'));
    }

    public function update(ReviewUpdateRequest $request, Book $book, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('books.show', $book)
            ->with('success', 'レビューを更新しました');
    }

    public function destroy(Book $book, Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);
        $review->delete();

        return redirect()->route('books.show', $book)
            ->with('success', 'レビューを削除しました');
    }
}
