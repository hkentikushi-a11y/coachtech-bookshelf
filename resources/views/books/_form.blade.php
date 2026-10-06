@php $old = fn($key, $default = '') => old($key, $book?->{$key} ?? $default); @endphp

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">タイトル <span class="text-red-500">*</span></label>
    <input id="f_title" type="text" name="title" value="{{ $old('title') }}"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm @error('title') border-red-400 @enderror">
    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">著者名 <span class="text-red-500">*</span></label>
    <input id="f_author" type="text" name="author" value="{{ $old('author') }}"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm @error('author') border-red-400 @enderror">
    @error('author')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">ISBN（13桁） <span class="text-red-500">*</span></label>
    <input id="f_isbn" type="text" name="isbn" value="{{ $old('isbn') }}" maxlength="13"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm @error('isbn') border-red-400 @enderror">
    @error('isbn')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">出版日</label>
    <input id="f_published_date" type="date" name="published_date" value="{{ old('published_date', $book?->published_date?->toDateString()) }}"
           class="border border-gray-300 rounded px-3 py-2 text-sm @error('published_date') border-red-400 @enderror">
    @error('published_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">説明</label>
    <textarea id="f_description" name="description" rows="3"
              class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ $old('description') }}</textarea>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">画像URL</label>
    <input id="f_image_url" type="url" name="image_url" value="{{ $old('image_url') }}"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm @error('image_url') border-red-400 @enderror">
    @error('image_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        ジャンル <span class="text-red-500">*</span>
    </label>
    @error('genre_ids')<p class="text-red-500 text-xs mb-1">{{ $message }}</p>@enderror
    <div class="flex flex-wrap gap-3">
        @foreach($genres as $genre)
            @php
                $selectedGenreIds = old('genre_ids', $book?->genres->pluck('id')->toArray() ?? []);
            @endphp
            <label class="flex items-center gap-1 text-sm">
                <input type="checkbox" name="genre_ids[]" value="{{ $genre->id }}"
                       {{ in_array($genre->id, $selectedGenreIds) ? 'checked' : '' }}>
                {{ $genre->name }}
            </label>
        @endforeach
    </div>
</div>
