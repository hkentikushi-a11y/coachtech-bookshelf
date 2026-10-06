@extends('layouts.app')

@section('title', '読書計画を編集 - BookShelf')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">読書計画を編集</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4 p-3 bg-gray-50 rounded">
            <p class="text-xs text-gray-500 mb-0.5">書籍</p>
            <p class="text-sm font-medium text-gray-800">{{ $readingPlan->book->title }}</p>
            <p class="text-xs text-gray-400">{{ $readingPlan->book->author }}</p>
        </div>

        <form method="POST" action="{{ route('reading-plans.update', $readingPlan) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    状態 <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    @foreach(['want' => '読みたい', 'reading' => '読んでいる', 'done' => '読了'] as $val => $label)
                        <label class="flex items-center gap-1.5 text-sm">
                            <input type="radio" name="status" value="{{ $val }}"
                                   {{ old('status', $readingPlan->status->value) === $val ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">目標日</label>
                <input type="date" name="target_date"
                       value="{{ old('target_date', $readingPlan->target_date?->format('Y-m-d')) }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm @error('target_date') border-red-400 @enderror">
                @error('target_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">開始日</label>
                <input type="date" name="started_at"
                       value="{{ old('started_at', $readingPlan->started_at?->format('Y-m-d')) }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">読了日</label>
                <input type="date" name="finished_at"
                       value="{{ old('finished_at', $readingPlan->finished_at?->format('Y-m-d')) }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 text-sm">
                    更新する
                </button>
                <a href="{{ route('reading-plans.index') }}"
                   class="text-sm text-gray-500 hover:underline self-center">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
