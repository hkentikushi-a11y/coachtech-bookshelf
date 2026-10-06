@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">レビュー編集</h1>
    <div class="bg-white rounded shadow p-6">
        <p class="text-sm text-gray-500 mb-4">書籍：<a href="{{ route('books.show', $book) }}" class="text-indigo-600 hover:underline">{{ $book->title }}</a></p>

        <form method="POST" action="{{ route('reviews.update', [$book, $review]) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">評価 <span class="text-red-500">*</span></label>
                <div class="flex gap-3">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="flex items-center gap-1 text-sm">
                        <input type="radio" name="rating" value="{{ $i }}"
                               {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                        {{ $i }}
                    </label>
                    @endfor
                </div>
                @error('rating')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">コメント <span class="text-red-500">*</span></label>
                <textarea name="comment" rows="4"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm @error('comment') border-red-400 @enderror">{{ old('comment', $review->comment) }}</textarea>
                @error('comment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700 text-sm">
                    更新する
                </button>
                <a href="{{ route('books.show', $book) }}" class="text-sm text-gray-500 hover:underline self-center">キャンセル</a>
            </div>
        </form>
    </div>
</div>
@endsection
