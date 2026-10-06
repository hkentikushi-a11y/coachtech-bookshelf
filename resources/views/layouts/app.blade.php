<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'BookShelf'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
<header class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
    <nav class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
        <a href="{{ route('books.index') }}"
           class="text-xl font-bold text-indigo-600 tracking-tight hover:text-indigo-700 transition">
            BookShelf
        </a>
        <div class="flex items-center gap-1 sm:gap-3">
            <a href="{{ route('books.index') }}"
               class="text-sm px-3 py-1.5 rounded-md {{ request()->routeIs('books.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }} transition">
                書籍
            </a>
            <a href="{{ route('ranking.index') }}"
               class="text-sm px-3 py-1.5 rounded-md {{ request()->routeIs('ranking.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }} transition">
                ランキング
            </a>
            <a href="{{ route('genres.index') }}"
               class="text-sm px-3 py-1.5 rounded-md {{ request()->routeIs('genres.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }} transition">
                ジャンル
            </a>
            @auth
                <a href="{{ route('favorites.index') }}"
                   class="text-sm px-3 py-1.5 rounded-md {{ request()->routeIs('favorites.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }} transition">
                    お気に入り
                </a>
                <a href="{{ route('report.index') }}"
                   class="text-sm px-3 py-1.5 rounded-md {{ request()->routeIs('report.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }} transition">
                    レポート
                </a>
                <a href="{{ route('reading-plans.index') }}"
                   class="text-sm px-3 py-1.5 rounded-md {{ request()->routeIs('reading-plans.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }} transition">
                    読書計画
                </a>
                @php $unreadCount = auth()->user()->customNotifications()->whereNull('read_at')->count(); @endphp
                <a href="{{ route('notifications.index') }}"
                   class="relative text-sm px-2 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 transition">
                    🔔
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center leading-none">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('books.create') }}"
                   class="text-sm bg-indigo-600 text-white px-3 py-1.5 rounded-md hover:bg-indigo-700 transition font-medium">
                    書籍登録
                </a>
                <form method="POST" action="/logout" class="inline">
                    @csrf
                    <button aria-label="ログアウト"
                            class="text-sm text-gray-500 hover:text-gray-700 px-2 py-1.5 rounded-md hover:bg-gray-100 transition">
                        ログアウト
                    </button>
                </form>
            @else
                <a href="/login"
                   class="text-sm text-indigo-600 font-medium px-3 py-1.5 rounded-md hover:bg-indigo-50 transition">
                    ログイン
                </a>
                <a href="/register"
                   class="text-sm bg-indigo-600 text-white px-3 py-1.5 rounded-md hover:bg-indigo-700 transition font-medium">
                    新規登録
                </a>
            @endauth
        </div>
    </nav>
</header>

@if(session('success') || session('error'))
<div class="max-w-5xl mx-auto px-4 mt-4">
    @if(session('success'))
        <div role="alert" class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <span>✓</span><span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div role="alert" class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <span>✕</span><span>{{ session('error') }}</span>
        </div>
    @endif
</div>
@endif

<main class="max-w-5xl mx-auto px-4 py-6 flex-1 w-full">
    @yield('content')
</main>

<footer class="border-t border-gray-200 mt-auto py-4 text-center text-xs text-gray-400">
    &copy; {{ date('Y') }} BookShelf
</footer>
</body>
</html>
