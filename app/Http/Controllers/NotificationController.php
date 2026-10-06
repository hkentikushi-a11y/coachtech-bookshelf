<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()
            ->customNotifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        // 未読をすべて既読にする
        auth()->user()
            ->customNotifications()
            ->unread()
            ->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }
}
