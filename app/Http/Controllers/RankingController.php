<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\View;

class RankingController extends Controller
{
    public function index(): View
    {
        $books = Book::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with('genres')
            ->has('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->limit(10)
            ->get();

        return view('ranking.index', compact('books'));
    }
}
