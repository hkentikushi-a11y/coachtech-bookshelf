<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // ── Task 2: 基本サマリー ─────────────────────────────────────────
        $totalReviews = $user->reviews()->count();
        $avgRating = $totalReviews > 0
            ? round($user->reviews()->avg('rating'), 1)
            : null;
        $booksReviewed = $user->reviews()->distinct('book_id')->count('book_id');
        $favoritesCount = $user->favorites()->count();

        // ── Task 3: 評価分布（1〜5 星ごとのレビュー数） ──────────────────
        $rawDist = $user->reviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        // 1〜5 を必ず揃える（ゼロ埋め）
        $ratingDistribution = collect(range(5, 1))->mapWithKeys(
            fn ($star) => [$star => $rawDist->get($star, 0)]
        );

        // ── Task 4: 高評価書籍 TOP5 ──────────────────────────────────────
        $topBooks = $user->reviews()
            ->with('book.genres')
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Task 5: ジャンル別評価傾向 TOP5 ──────────────────────────────
        $genreRatings = Genre::select('genres.id', 'genres.name')
            ->selectRaw('ROUND(AVG(reviews.rating), 1) AS avg_rating, COUNT(reviews.id) AS review_count')
            ->join('book_genre', 'genres.id', '=', 'book_genre.genre_id')
            ->join('reviews', 'book_genre.book_id', '=', 'reviews.book_id')
            ->where('reviews.user_id', $user->id)
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get();

        return view('reports.index', compact(
            'totalReviews', 'avgRating', 'booksReviewed', 'favoritesCount',
            'ratingDistribution', 'topBooks', 'genreRatings'
        ));
    }
}
