@extends('layouts.app')

@section('title', 'マイ読書レポート - BookShelf')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">マイ読書レポート</h1>

    {{-- Task 2: 基本サマリー --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-indigo-600">{{ $totalReviews }}</p>
            <p class="text-xs text-gray-500 mt-1">総レビュー数</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-yellow-500">
                {{ $avgRating !== null ? number_format($avgRating, 1) : '—' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">平均評価</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $booksReviewed }}</p>
            <p class="text-xs text-gray-500 mt-1">レビュー済み書籍</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-pink-500">{{ $favoritesCount }}</p>
            <p class="text-xs text-gray-500 mt-1">お気に入り</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- Task 3: 評価分布 --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="text-base font-semibold text-gray-700 mb-4">評価分布</h2>
            @if($totalReviews === 0)
                <p class="text-sm text-gray-400 text-center py-4">まだレビューがありません</p>
            @else
                <div class="space-y-2">
                    @foreach($ratingDistribution as $star => $count)
                        @php $pct = $totalReviews > 0 ? round($count / $totalReviews * 100) : 0; @endphp
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-14 text-right text-gray-600 shrink-0">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $star ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                @endfor
                            </span>
                            <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div class="bg-yellow-400 h-3 rounded-full transition-all"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="w-8 text-right text-gray-500 shrink-0">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Task 5: ジャンル別評価傾向 TOP5 --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="text-base font-semibold text-gray-700 mb-4">ジャンル別評価傾向 TOP5</h2>
            @if($genreRatings->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">データがありません</p>
            @else
                <ol class="space-y-3">
                    @foreach($genreRatings as $i => $genre)
                        <li class="flex items-center gap-3">
                            <span class="w-5 text-center text-xs font-bold text-gray-400">{{ $i + 1 }}</span>
                            <span class="flex-1 text-sm text-gray-700 truncate">{{ $genre->name }}</span>
                            <span class="text-yellow-500 text-sm font-bold">
                                ★ {{ number_format($genre->avg_rating, 1) }}
                            </span>
                            <span class="text-xs text-gray-400">
                                ({{ $genre->review_count }}件)
                            </span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    {{-- Task 4: 高評価書籍 TOP5 --}}
    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="text-base font-semibold text-gray-700 mb-4">高評価書籍 TOP5</h2>
        @if($topBooks->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">まだレビューがありません</p>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($topBooks as $i => $review)
                    <div class="flex items-start gap-4 py-3">
                        <span class="shrink-0 w-6 text-center font-bold text-gray-300 text-sm mt-0.5">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('books.show', $review->book) }}"
                               class="text-sm font-medium text-indigo-600 hover:underline truncate block">
                                {{ $review->book->title }}
                            </a>
                            <p class="text-xs text-gray-500">{{ $review->book->author }}</p>
                            @if($review->comment)
                                <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $review->comment }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="text-yellow-500 font-bold text-sm">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="{{ $s <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                @endfor
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
