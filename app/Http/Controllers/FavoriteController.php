<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $books = auth()->user()->favorites()
            ->with(['genres', 'reviews'])
            ->paginate(10);

        return view('favorites.index', compact('books'));
    }

    public function toggle(Book $book): RedirectResponse
    {
        $user = auth()->user();

        if ($user->favorites()->where('book_id', $book->id)->exists()) {
            $user->favorites()->detach($book->id);
            $message = 'お気に入りを解除しました';
        } else {
            $user->favorites()->attach($book->id);
            $message = 'お気に入りに追加しました';
        }

        return back()->with('success', $message);
    }
}
