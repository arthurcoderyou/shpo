<?php

use Livewire\Volt\Component;
use App\Livewire\Actions\Logout;

new class extends Component {

     protected $listeners = [ 
        'notificationsCreated' => '$refresh',
        'notificationsDeleted' => '$refresh',
        'notificationsUpdated' => '$refresh',
        
        'projectTimerUpdated' => '$refresh',
        'documentTypeCreated' => '$refresh',
        'documentTypeUpdated' => '$refresh',
        'documentTypeDeleted' => '$refresh',
        'activitylogCreated' => '$refresh',
        'reviewerCreated' => '$refresh', 
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
    ];

    /** @var 'desktop'|'mobile' */
    public string $variant = 'desktop';

    public bool $showLogo = true;
    public bool $showNotifications = true;

    /** Optional logo src override */
    public ?string $logoSrc = null;
 

    /** Display name fallback */
    public ?string $displayName = null;

    public function mount(
        string $variant = 'desktop',
        bool $showLogo = true,
        bool $showNotifications = true,
        ?string $logoSrc = null, 
        ?string $displayName = null,
    ): void {
        $this->variant = in_array($variant, ['desktop','mobile'], true) ? $variant : 'desktop';
        $this->showLogo = $showLogo;
        $this->showNotifications = $showNotifications;
        $this->logoSrc = $logoSrc ?? asset('images/logo-ghrd.png'); 
        $this->displayName = $displayName ?? (auth()->user()->name ?? 'Guest');
    }


    /** Livewire 3 computed property */
    public function getUnreadCountProperty(): int
    {
        if (!Auth::check()) return 0;

        // Your provided query:
        return Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->count();
    }


     /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {

        // Destroy session
        Session::flush();
        
        $logout();

        $this->redirect('/', navigate: true);
    }


};
?>

<div
    @class([
        // wrapper flex (desktop shows app-left + app-right in a row; mobile identical but you can style differently if needed)
        'w-full flex items-center justify-between gap-3',
        'px-2 sm:px-3',
    ])
>
    {{-- Left: Logo (optional) --}}
    @if($showLogo)
        <div class="flex items-center gap-3">
            <img src="{{ $logoSrc }}" alt="Logo" class="h-8" />
        </div>
    @endif

    {{-- Right: Actions --}}
    <div class="flex items-center gap-3">
        @guest
            @if($variant === 'mobile')
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg border hover:bg-gray-50">
                    Sign in
                </a>
            @endif
        @endguest

        @auth
            {{-- Notifications (optional) --}}
            @if($showNotifications)
                <button
                    type="button"
                    class="relative rounded-full p-2 hover:bg-gray-100"
                    @click="$dispatch('open-notifications')"
                    aria-label="Open notifications"
                >
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 8 7.388 8 9v5.159c0 .538-.214 1.055-.595 1.436L6 17h5"/>
                    </svg>

                    {{-- Red dot when there are unread notifications --}}
                    {{-- @if($this->unreadCount > 0)
                        <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                    @endif --}}

                    {{-- Optional numeric badge (uncomment if you want a number) --}}
                    @if($this->unreadCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full text-[10px] leading-5 text-white bg-red-500 text-center">
                            {{ $this->unreadCount }}
                        </span>
                    @endif
                </button>
            @endif

            {{-- Profile Dropdown (Alpine) --}}
            <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                <button
                    type="button"
                    @click="open = !open"
                    class="flex items-center gap-2 rounded-full border px-2 py-1 hover:bg-gray-50"
                    aria-haspopup="menu"
                    :aria-expanded="open"
                >
                    <img src="{{ asset('images/avatar-placeholder.png') }}" alt="Avatar" class="w-8 h-8 rounded-full" />
                    <span class="hidden sm:inline text-sm font-medium">{{ $displayName }}</span>
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Menu --}}
                <div
                    x-cloak
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    class="absolute right-0 mt-2 w-56 bg-white border rounded-xl shadow-lg text-sm overflow-hidden"
                    role="menu"
                >
                    <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-50" wire:navigate role="menuitem">Profile</a>
                    @can('system access global admin')
                    <a href="{{ route('setting.index') }}" class="block px-4 py-2 hover:bg-gray-50" wire:navigate role="menuitem">Settings</a>
                    @endcan
                     
                    <button type="button" wire:click="logout"
                            class="w-full text-left block px-4 py-2 text-rose-600 hover:bg-gray-50"
                            role="menuitem">
                        Sign out
                    </button> 
                </div>
            </div>
        @endauth
    </div>
</div>
