@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">ジャンル登録</h1>
    <div class="bg-white rounded shadow p-6">
        <form method="POST" action="{{ route('genres.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    ジャンル名 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700 text-sm">登録する</button>
                <a href="{{ route('genres.index') }}" class="text-sm text-gray-500 hover:underline self-center">キャンセル</a>
            </div>
        </form>
    </div>
</div>
@endsection
