@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">書籍ランキング TOP10</h1>

@if($books->isEmpty())
    <p class="text-gray-500 text-center py-12">レビューが投稿された書籍がありません。</p>
@else
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-indigo-600 text-white">
            <tr>
                <th class="px-4 py-3 text-center w-12">順位</th>
                <th class="px-4 py-3 text-left">書籍</th>
                <th class="px-4 py-3 text-left hidden md:table-cell">著者</th>
                <th class="px-4 py-3 text-left hidden lg:table-cell">ジャンル</th>
                <th class="px-4 py-3 text-center">平均評価</th>
                <th class="px-4 py-3 text-center">件数</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($books as $i => $book)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-center font-bold
                    {{ $i === 0 ? 'text-yellow-500 text-lg' : ($i === 1 ? 'text-gray-400 text-base' : ($i === 2 ? 'text-amber-600 text-base' : 'text-gray-500')) }}">
                    @if($i === 0) 🥇
                    @elseif($i === 1) 🥈
                    @elseif($i === 2) 🥉
                    @else {{ $i + 1 }}
                    @endif
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('books.show', $book) }}"
                       class="text-indigo-600 hover:underline font-medium">{{ $book->title }}</a>
                </td>
                <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $book->author }}</td>
                <td class="px-4 py-3 hidden lg:table-cell">
                    <div class="flex flex-wrap gap-1">
                        @foreach($book->genres as $genre)
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">{{ $genre->name }}</span>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="text-yellow-400 font-bold">★</span>
                    <span class="font-semibold">{{ number_format($book->reviews_avg_rating, 1) }}</span>
                </td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $book->reviews_count }}件</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
