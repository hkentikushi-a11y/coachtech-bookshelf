@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">書籍一覧</h1>
    @auth
        <a href="{{ route('books.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
            書籍を登録する
        </a>
    @endauth
</div>

{{-- 検索フォーム --}}
<form method="GET" action="{{ route('books.index') }}" class="bg-white rounded shadow p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs text-gray-500 mb-1">キーワード（タイトル・著者）</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="例：夏目漱石"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
        </div>
        <div class="min-w-36">
            <label class="block text-xs text-gray-500 mb-1">ジャンル</label>
            <select name="genre_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">すべて</option>
                @foreach($genres as $genre)
                    <option value="{{ $genre->id }}" {{ request('genre_id') == $genre->id ? 'selected' : '' }}>
                        {{ $genre->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <label class="block text-xs text-gray-500 mb-1">並び順</label>
            <select name="sort" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>新着順</option>
                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>評価順</option>
                <option value="title"  {{ request('sort') === 'title'  ? 'selected' : '' }}>タイトル順</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                検索
            </button>
            @if(request()->hasAny(['q', 'genre_id', 'sort']))
            <a href="{{ route('books.index') }}"
               class="bg-gray-100 text-gray-600 px-4 py-2 rounded text-sm hover:bg-gray-200">
                クリア
            </a>
            @endif
        </div>
    </div>
</form>

{{-- 件数表示 --}}
<p class="text-sm text-gray-500 mb-4">
    {{ $books->total() }}冊の書籍
    @if(request('q'))
        「<span class="font-medium text-gray-700">{{ request('q') }}</span>」の検索結果
    @endif
</p>

@if($books->isEmpty())
    <p class="text-gray-500 text-center py-12">条件に一致する書籍が見つかりませんでした。</p>
@else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($books as $book)
        <div class="relative bg-white rounded shadow hover:shadow-md transition overflow-hidden">
            <a href="{{ route('books.show', $book) }}" class="block">
                <img src="{{ $book->image_url ?: 'https://placehold.co/200x280?text=No+Image' }}"
                     alt="{{ $book->title }}" class="w-full h-48 object-cover">
                <div class="p-3">
                    <p class="font-semibold text-sm text-gray-800 truncate">{{ $book->title }}</p>
                    <p class="text-xs text-gray-500 truncate mb-2">{{ $book->author }}</p>
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach($book->genres as $genre)
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">
                                {{ $genre->name }}
                            </span>
                        @endforeach
                    </div>
                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <span class="text-yellow-400">★</span>
                        <span>{{ $book->reviews->count() > 0 ? number_format($book->reviews->avg('rating'), 1) : '-' }}</span>
                        <span class="ml-1">({{ $book->reviews->count() }}件)</span>
                    </div>
                </div>
            </a>
            @auth
            <form method="POST" action="{{ route('favorites.toggle', $book) }}"
                  class="absolute top-2 right-2">
                @csrf
                <button title="{{ in_array($book->id, $favoritedIds) ? 'お気に入り解除' : 'お気に入り登録' }}"
                        class="text-xl {{ in_array($book->id, $favoritedIds) ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300' }}">
                    ★
                </button>
            </form>
            @endauth
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $books->links() }}</div>
@endif
@endsection
