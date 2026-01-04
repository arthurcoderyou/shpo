<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public bool $open = false;

    /** Paginated rows (computed) */
    public function getRowsProperty()
    {
        if (!Auth::check()) {
            // return a LengthAwarePaginator-like empty structure to keep blade simple
            return collect();
        }

        return Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);
    }

    public function markRead(string $id): void
    {
        if (!Auth::check()) return;

        $n = Auth::user()->notifications()->whereKey($id)->first();
        if ($n && is_null($n->read_at)) {
            $n->markAsRead();
        }

        $this->dispatch('notifications-changed');
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        if (!Auth::check()) return;

        $n = Auth::user()->notifications()->whereKey($id)->first();
        if ($n) $n->delete();

        $this->dispatch('notifications-changed');
        $this->resetPage();
    }

    public function markAllRead(): void
    {
        if (!Auth::check()) return;

        Auth::user()->unreadNotifications->markAsRead();

        $this->dispatch('notifications-changed');
        $this->resetPage();
    }

    public function clearAll(): void
    {
        if (!Auth::check()) return;

        Auth::user()->notifications()->delete();

        $this->dispatch('notifications-changed');
        $this->resetPage();
    }
};
?>

{{-- Off-canvas Notifications --}}
<div
    x-data="{ open: @entangle('open').live }"
    x-on:open-notifications.window="open = true"
    x-on:keydown.escape.window="open = false"
    wire:poll.keep-alive.60s
>
    {{-- Backdrop --}}
    <div
        x-cloak

        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black/30 z-40"
        @click="open = false"
    ></div>

    {{-- Panel --}}
    <aside
        x-cloak
        id="notif-offcanvas"
        class="fixed inset-y-0 right-0 w-full sm:w-[28rem] bg-white border-l z-50 transition-transform duration-200 ease-out"
        :class="open ? 'translate-x-0' : 'translate-x-full'"
    >
        <div class="h-full flex flex-col">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <h3 class="font-semibold">Notifications</h3>

                <div class="flex items-center gap-2">
                    @auth
                        <button
                            wire:click="markAllRead"
                            class="rounded-lg border px-2 py-1 text-sm hover:bg-gray-50"
                            title="Mark all as read"
                        >Mark all read</button>

                        {{-- <button
                            wire:click="clearAll"
                            class="rounded-lg border px-2 py-1 text-sm hover:bg-gray-50 text-rose-600"
                            title="Delete all notifications"
                            onclick="return confirm('Delete all notifications?')"
                        >Clear all</button> --}}

                        <button
                            x-data
                            x-on:click.prevent="
                                if (confirm('Delete all notifications?')) {
                                    $wire.clearAll()
                                }
                            "
                            class="rounded-lg border px-2 py-1 text-sm hover:bg-gray-50 text-rose-600"
                            title="Delete all notifications"
                        >
                            Clear all
                        </button>


                    @endauth

                    <button
                        class="rounded-lg border px-2 py-1 text-sm hover:bg-gray-50"
                        @click="open = false"
                    >Close</button>
                </div>
            </div>

            {{-- List --}}
            <div class="flex-1 overflow-y-auto divide-y">
                @guest
                    <div class="px-4 py-6 text-sm text-slate-500">
                        Please <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">sign in</a> to view notifications.
                    </div>
                @endguest

                @auth
                    @php
                        // Pull rows OUTSIDE of the @forelse to avoid Blade pairing issues
                        $rows = $this->rows;
                    @endphp

                    @forelse($rows as $n)
                        @php
                            $data = $n->data ?? [];
                            $title = $data['title'] ?? ($data['subject'] ?? 'Notification');
                            $message = $data['message'] ?? ($data['body'] ?? null);
                            $url = $data['url'] ?? null;
                            $isUnread = is_null($n->read_at);
                        @endphp

                        <div class="px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium truncate">{{ $title }}</p>
                                        @if($isUnread)
                                            <span class="inline-block h-2 w-2 rounded-full bg-red-500 mt-1"></span>
                                        @endif
                                    </div>

                                    @if($message)
                                        <p class="text-slate-600 text-sm mt-1">
                                            {{ is_string($message) ? $message : json_encode($message) }}
                                        </p>
                                    @endif

                                    {{-- Redirect link only when data["url"] is not empty --}}
                                    @if(!empty($url))
                                        <div class="mt-2">
                                            <a
                                                href="{{ $url }}"
                                                class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700 hover:underline"
                                                onclick="@this.markRead('{{ $n->id }}')"
                                            >
                                                Open
                                            </a>
                                        </div>
                                    @endif

                                    <p class="text-slate-400 text-xs mt-1">
                                        {{ $n->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($isUnread)
                                        <button
                                            wire:click="markRead('{{ $n->id }}')"
                                            class="rounded-lg border px-2 py-1 text-xs hover:bg-gray-50"
                                            title="Mark as read"
                                        >Read</button>
                                    @endif

                                    <button
                                        wire:click="delete('{{ $n->id }}')"
                                        class="rounded-lg border px-2 py-1 text-xs hover:bg-gray-50 text-rose-600"
                                        title="Delete"
                                    >Delete</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-sm text-slate-500">
                            No notifications yet.
                        </div>
                    @endforelse

                    {{-- Pager --}}
                    @if(is_object($rows) && method_exists($rows, 'hasPages') && $rows->hasPages())
                        <div class="px-4 py-3 border-t bg-white">
                            {{ $rows->onEachSide(0)->links() }}
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </aside>
</div>
