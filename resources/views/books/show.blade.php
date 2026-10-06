@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- 書籍詳細 --}}
    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="flex gap-6">
            <img src="{{ $book->image_url ?: 'https://placehold.co/150x200?text=No+Image' }}"
                 alt="{{ $book->title }}" class="w-36 h-48 object-cover rounded flex-shrink-0">
            <div class="flex-1">
                <div class="flex items-start justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 mb-1">{{ $book->title }}</h1>
                    @auth
                    <form method="POST" action="{{ route('favorites.toggle', $book) }}" class="ml-3 flex-shrink-0">
                        @csrf
                        <button title="{{ $isFavorited ? 'お気に入り解除' : 'お気に入り登録' }}"
                                class="text-2xl {{ $isFavorited ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300' }}">★</button>
                    </form>
                    @endauth
                </div>
                <p class="text-gray-600 mb-2">{{ $book->author }}</p>
                @if($book->published_date)
                    <p class="text-sm text-gray-400 mb-2">{{ $book->published_date->format('Y年m月d日') }}</p>
                @endif
                <div class="flex flex-wrap gap-1 mb-3">
                    @foreach($book->genres as $genre)
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">{{ $genre->name }}</span>
                    @endforeach
                </div>
                @if($book->description)
                    <p class="text-sm text-gray-700">{{ $book->description }}</p>
                @endif
                @can('update', $book)
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('books.edit', $book) }}"
                       class="text-sm bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">編集</a>
                    <form method="POST" action="{{ route('books.destroy', $book) }}"
                          onsubmit="return confirm('削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">削除</button>
                    </form>
                </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- レビュー一覧 --}}
    <h2 class="text-lg font-bold text-gray-700 mb-3">レビュー ({{ $book->reviews->count() }}件)</h2>

    @forelse($book->reviews as $review)
    <div class="bg-white rounded shadow p-4 mb-3">
        <div class="flex items-center justify-between mb-1">
            <span class="font-medium text-sm text-gray-700">{{ $review->user->name }}</span>
            <div class="flex items-center gap-2">
                <span class="text-yellow-400 text-sm">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= $review->rating ? '★' : '☆' }}
                    @endfor
                </span>
                @can('update', $review)
                <a href="{{ route('reviews.edit', [$book, $review]) }}"
                   class="text-xs text-indigo-600 hover:underline">編集</a>
                <form method="POST" action="{{ route('reviews.destroy', [$book, $review]) }}"
                      onsubmit="return confirm('削除しますか？')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button class="text-xs text-red-500 hover:underline">削除</button>
                </form>
                @endcan
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-2">{{ $review->comment }}</p>
        {{-- いいねボタン --}}
        @auth
        <form method="POST" action="{{ route('reviews.like', $review) }}" class="inline">
            @csrf
            @php $liked = in_array($review->id, $likedReviewIds); @endphp
            <button class="text-xs flex items-center gap-1 {{ $liked ? 'text-pink-500' : 'text-gray-400 hover:text-pink-400' }}">
                <span>♥</span>
                <span>{{ $review->likes->count() }}</span>
            </button>
        </form>
        @else
        <span class="text-xs text-gray-400 flex items-center gap-1">
            <span>♥</span><span>{{ $review->likes->count() }}</span>
        </span>
        @endauth
    </div>
    @empty
    <p class="text-gray-400 text-sm mb-4">レビューはまだありません。</p>
    @endforelse

    {{-- レビュー投稿フォーム --}}
    @auth
        @php $alreadyReviewed = $book->reviews->where('user_id', auth()->id())->isNotEmpty(); @endphp
        @unless($alreadyReviewed)
        <div class="bg-white rounded shadow p-6 mt-6">
            <h3 class="text-base font-bold text-gray-700 mb-4">レビューを投稿する</h3>
            <form method="POST" action="{{ route('reviews.store', $book) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">評価 <span class="text-red-500">*</span></label>
                    <div class="flex gap-3">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="flex items-center gap-1 text-sm">
                            <input type="radio" name="rating" value="{{ $i }}"
                                   {{ old('rating') == $i ? 'checked' : '' }}>
                            {{ $i }}
                        </label>
                        @endfor
                    </div>
                    @error('rating')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">コメント <span class="text-red-500">*</span></label>
                    <textarea name="comment" rows="3"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm @error('comment') border-red-400 @enderror"
                              placeholder="この書籍の感想を書いてください">{{ old('comment') }}</textarea>
                    @error('comment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700 text-sm">
                    投稿する
                </button>
            </form>
        </div>
        @endunless
    @endauth
</div>
@endsection
