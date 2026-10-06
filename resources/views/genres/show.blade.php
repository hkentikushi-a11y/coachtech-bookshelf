@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        ジャンル：<span class="text-indigo-600">{{ $genre->name }}</span>
    </h1>
    <a href="{{ route('genres.index') }}" class="text-sm text-gray-500 hover:underline">← ジャンル一覧</a>
</div>

@if($books->isEmpty())
    <p class="text-gray-500 text-center py-12">このジャンルに登録された書籍はありません。</p>
@else
    <p class="text-sm text-gray-500 mb-4">{{ $books->total() }}冊の書籍が登録されています</p>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($books as $book)
        <a href="{{ route('books.show', $book) }}"
           class="bg-white rounded shadow hover:shadow-md transition overflow-hidden block">
            <img src="{{ $book->image_url ?: 'https://placehold.co/200x280?text=No+Image' }}"
                 alt="{{ $book->title }}" class="w-full h-48 object-cover">
            <div class="p-3">
                <p class="font-semibold text-sm text-gray-800 truncate">{{ $book->title }}</p>
                <p class="text-xs text-gray-500 truncate mb-2">{{ $book->author }}</p>
                <div class="flex flex-wrap gap-1 mb-2">
                    @foreach($book->genres as $g)
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">{{ $g->name }}</span>
                    @endforeach
                </div>
                <div class="text-xs text-gray-500 flex items-center gap-1">
                    <span>★</span>
                    <span>{{ $book->reviews->count() > 0 ? number_format($book->reviews->avg('rating'), 1) : '-' }}</span>
                    <span class="ml-1">({{ $book->reviews->count() }}件)</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-6">{{ $books->links() }}</div>
@endif
@endsection
