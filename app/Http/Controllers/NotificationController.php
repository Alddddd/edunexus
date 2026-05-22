<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    public function read(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return redirect()->to($this->safeActionUrl($notification->data['action_url'] ?? null));
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    private function safeActionUrl(?string $actionUrl): string
    {
        if (blank($actionUrl)) {
            return route('dashboard');
        }

        $path = parse_url($actionUrl, PHP_URL_PATH) ?: '/dashboard';
        $query = parse_url($actionUrl, PHP_URL_QUERY);
        $pathWithQuery = $path . ($query ? '?' . $query : '');

        if (Str::contains($path, ['/logout']) || ! Str::startsWith($path, '/')) {
            return route('dashboard');
        }

        $request = request()->create($path, 'GET');

        try {
            Route::getRoutes()->match($request);
        } catch (\Throwable) {
            return route('dashboard');
        }

        return url($pathWithQuery);
    }
}
