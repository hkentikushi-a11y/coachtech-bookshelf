@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">書籍編集</h1>
    <div class="bg-white rounded shadow p-6">
        <form method="POST" action="{{ route('books.update', $book) }}">
            @csrf
            @method('PUT')
            @include('books._form', ['book' => $book])
            <div class="mt-6">
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 text-sm">
                    更新する
                </button>
                <a href="{{ route('books.show', $book) }}" class="ml-3 text-sm text-gray-500 hover:underline">キャンセル</a>
            </div>
        </form>
    </div>
</div>
@endsection
