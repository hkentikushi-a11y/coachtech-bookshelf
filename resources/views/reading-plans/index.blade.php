@extends('layouts.app')

@section('title', '読書計画 - BookShelf')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">読書計画</h1>
        <a href="{{ route('reading-plans.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
            ＋ 計画を追加
        </a>
    </div>

    @if($plans->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
            <p class="text-sm">読書計画がありません。書籍を追加してみましょう。</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">書籍</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600 w-24">状態</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600 w-28">目標日</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600 w-36">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($plans as $plan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('books.show', $plan->book) }}"
                                   class="text-indigo-600 hover:underline font-medium">
                                    {{ $plan->book->title }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $plan->book->author }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->status->badgeClass() }}">
                                    {{ $plan->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                {{ $plan->target_date ? $plan->target_date->format('Y/m/d') : '—' }}
                                @if($plan->target_date && $plan->status->value === 'reading')
                                    @php $diff = now()->diffInDays($plan->target_date, false); @endphp
                                    @if($diff < 0)
                                        <span class="block text-red-500">{{ abs($diff) }}日超過</span>
                                    @elseif($diff <= 3)
                                        <span class="block text-orange-500">あと{{ $diff }}日</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($plan->status->value === 'reading')
                                        <form method="POST" action="{{ route('reading-plans.done', $plan) }}">
                                            @csrf
                                            <button class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                                                読了
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('reading-plans.edit', $plan) }}"
                                       class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded hover:bg-gray-200">
                                        編集
                                    </a>
                                    <form method="POST" action="{{ route('reading-plans.destroy', $plan) }}"
                                          onsubmit="return confirm('削除しますか？')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200">
                                            削除
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $plans->links() }}</div>
    @endif
</div>
@endsection
