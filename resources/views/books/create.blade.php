@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">書籍登録</h1>

    {{-- ISBN検索パネル --}}
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6"
         x-data="{
             isbn: '',
             loading: false,
             errorMsg: '',
             async fetchIsbn() {
                 const raw = this.isbn.replace(/[^0-9]/g, '');
                 if (raw.length !== 13) { this.errorMsg = 'ISBNは13桁で入力してください'; return; }
                 this.loading = true; this.errorMsg = '';
                 try {
                     const res = await fetch('/api/isbn/' + raw, { headers: { 'Accept': 'application/json' } });
                     if (!res.ok) { const d = await res.json(); this.errorMsg = d.message || '取得に失敗しました'; return; }
                     const { data } = await res.json();
                     if (data.title)          document.getElementById('f_title').value          = data.title;
                     if (data.author)         document.getElementById('f_author').value         = data.author;
                     if (data.published_date) document.getElementById('f_published_date').value = data.published_date;
                     if (data.description)    document.getElementById('f_description').value    = data.description;
                     if (data.image_url)      document.getElementById('f_image_url').value      = data.image_url;
                     document.getElementById('f_isbn').value = raw;
                 } catch(e) {
                     this.errorMsg = '通信エラーが発生しました';
                 } finally {
                     this.loading = false;
                 }
             }
         }">
        <p class="text-sm font-medium text-indigo-700 mb-2">ISBN で書籍情報を自動入力</p>
        <div class="flex gap-2">
            <input type="text" x-model="isbn" placeholder="例：9784101010014（ハイフン可）"
                   class="flex-1 border border-indigo-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                   @keydown.enter.prevent="fetchIsbn">
            <button type="button" @click="fetchIsbn" :disabled="loading"
                    class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 disabled:opacity-50 whitespace-nowrap">
                <span x-show="!loading">情報を取得</span>
                <span x-show="loading">取得中...</span>
            </button>
        </div>
        <p x-show="errorMsg" x-text="errorMsg" class="text-red-500 text-xs mt-1"></p>
    </div>

    {{-- 書籍登録フォーム --}}
    <div class="bg-white rounded shadow p-6">
        <form method="POST" action="{{ route('books.store') }}">
            @csrf
            @include('books._form', ['book' => null])
            <div class="mt-6">
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 text-sm">
                    登録する
                </button>
                <a href="{{ route('books.index') }}" class="ml-3 text-sm text-gray-500 hover:underline">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
