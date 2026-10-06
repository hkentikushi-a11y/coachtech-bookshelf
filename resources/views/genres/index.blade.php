@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">ジャンル一覧</h1>
    @auth
        <a href="{{ route('genres.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">ジャンルを登録する</a>
    @endauth
</div>

@if($genres->isEmpty())
    <p class="text-gray-500 text-center py-12">ジャンルが登録されていません。</p>
@else
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-gray-600">
            <tr>
                <th class="px-5 py-3 text-left">ジャンル名</th>
                <th class="px-5 py-3 text-center">書籍数</th>
                <th class="px-5 py-3 text-right">操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($genres as $genre)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <a href="{{ route('genres.show', $genre) }}"
                       class="text-indigo-600 hover:underline font-medium">{{ $genre->name }}</a>
                </td>
                <td class="px-5 py-3 text-center text-gray-500">{{ $genre->books_count }}冊</td>
                <td class="px-5 py-3 text-right">
                    @auth
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('genres.edit', $genre) }}"
                           class="text-xs bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">編集</a>
                        <form method="POST" action="{{ route('genres.destroy', $genre) }}"
                              onsubmit="return confirm('削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">削除</button>
                        </form>
                    </div>
                    @endauth
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
