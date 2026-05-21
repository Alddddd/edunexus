@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')

<div class="max-w-5xl">

    <div class="flex items-start justify-between flex-wrap gap-4 mb-8">

        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Notifications
            </h1>

            <p class="text-slate-500 mt-2">
                Review recent EduNexUs operational updates, approvals, claims, settlements, and verification activity.
            </p>
        </div>

        @if(auth()->user()->unreadNotifications->count() > 0)

            <form method="POST"
                  action="{{ route('notifications.mark-all-read') }}">
                @csrf

                <button type="submit"
                        class="btn-primary">
                    Mark All as Read
                </button>
            </form>

        @endif

    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="divide-y divide-slate-100">

            @forelse($notifications as $notification)

                <div class="px-6 py-5 hover:bg-slate-50 transition">

                    <div class="flex items-start gap-4">

                        <div class="mt-1">

                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                {{ is_null($notification->read_at)
                                    ? 'bg-teal-100 text-teal-700'
                                    : 'bg-slate-100 text-slate-500' }}">

                                <x-icon :name="is_null($notification->read_at) ? 'bell' : 'check'" size="h-5 w-5" />

                            </div>

                        </div>

                        <div class="flex-1">

                            <div class="flex items-start justify-between gap-4 flex-wrap">

                                <div>
                                    <p class="font-semibold text-slate-800">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </p>

                                    <p class="text-slate-500 mt-1">
                                        {{ $notification->data['message'] ?? 'No details available.' }}
                                    </p>
                                </div>

                                <div class="text-right">

                                    @if(isset($notification->data['status']))

                                        <x-status-badge :status="$notification->data['status']" :tone="$notification->data['status']" size="xs" />

                                    @endif

                                    <p class="text-xs text-slate-400 mt-2">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>

                                </div>

                            </div>

                            <div class="mt-4 flex items-center justify-between flex-wrap gap-3">

                                <div class="flex items-center gap-2">

                                    <x-status-badge
                                        :status="is_null($notification->read_at) ? 'Unread' : 'Read'"
                                        :tone="is_null($notification->read_at) ? 'warning' : 'neutral'"
                                        size="xs" />

                                </div>

                                <a href="{{ route('notifications.read', $notification->id) }}"
                                   class="btn-outline min-h-10 px-4 py-2">
                                    Open
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="px-6 py-16 text-center">

                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                        <x-icon name="bell" size="h-8 w-8" />
                    </div>

                    <p class="text-lg font-semibold text-slate-700">
                        No notifications yet
                    </p>

                    <p class="text-slate-500 mt-2">
                        Operational updates and workflow alerts will appear here.
                    </p>

                </div>

            @endforelse

        </div>

        <div class="border-t border-slate-100 bg-slate-50 px-6 py-4">
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-slate-500">
                    Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} notifications
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
