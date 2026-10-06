@extends('layouts.app')

@section('title', '通知 - BookShelf')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">通知</h1>

    @if($notifications->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
            <p class="text-sm">通知はありません。</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifications as $notification)
                @php $data = $notification->data; @endphp
                <div class="bg-white rounded-lg shadow px-5 py-4 border-l-4
                            {{ $notification->read_at ? 'border-gray-200' : 'border-indigo-400' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">
                                {{ $data['message'] ?? '通知があります' }}
                            </p>
                            @if(isset($data['book_title']))
                                <p class="text-xs text-indigo-600 mt-1">📚 {{ $data['book_title'] }}</p>
                            @endif
                            @if(isset($data['target_date']))
                                <p class="text-xs text-gray-400 mt-0.5">
                                    目標日: {{ $data['target_date'] }}
                                </p>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 shrink-0 ml-4">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
