<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\UserDeviceLog;
use App\Models\ActivityLog;
use RealRashid\SweetAlert\Facades\Alert;

new class extends Component {
    public ?User $user = null;
    public int $notificationCount = 0; // you can set this from DB later
    public ?string $show_device_trust_section = '';
    public bool $showNotifications = true;

    protected $listeners = [ 
        'systemEvent' => 'getUnreadCountProperty()',

        'notificationsCreated' => 'getUnreadCountProperty()',
        'notificationsDeleted' => 'getUnreadCountProperty()',
        'notificationsUpdated' => 'getUnreadCountProperty()',
        
        'projectTimerUpdated' => 'getUnreadCountProperty()',
        'documentTypeCreated' => 'getUnreadCountProperty()',
        'documentTypeUpdated' => 'getUnreadCountProperty()',
        'documentTypeDeleted' => 'getUnreadCountProperty()',
        'activitylogCreated' => 'getUnreadCountProperty()',
        'reviewerCreated' => 'getUnreadCountProperty()', 
        'reviewerUpdated' => 'getUnreadCountProperty()',
        'reviewerDeleted' => 'getUnreadCountProperty()',
        'notifications-changed' => 'getUnreadCountProperty()',
    ];

    public function mount(): void
    {
        $this->user = auth()->user();
        // Optionally load unread count here (e.g., from notifications table)
        // $this->notificationCount = auth()->user()?->unreadNotifications()->count() ?? 0;


        $user_device_log = UserDeviceLog::getUserDeviceLog();

        // Only show the section if the device is NOT trusted
        $this->show_device_trust_section = !$user_device_log->trusted;

        
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(\App\Livewire\Actions\Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function getAvatarUrlProperty(): string
    {
        $name = $this->user?->name ?: 'Guest User';
        $bg   = '3b82f6'; // blue-500
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . "&background={$bg}&color=fff";
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



    public function markDeviceAsTrusted($answer)
    {
        $user_device_log = UserDeviceLog::getUserDeviceLog();
        $user_device_log->trusted = $answer === 'yes';
        $user_device_log->save();

        // Hide the section after marking the device
        $this->show_device_trust_section = false;


        if($answer === 'yes'){
            ActivityLog::create([
                'log_action' => "Deviee trusted by \"".Auth::user()->name."\" ",
                'log_username' => Auth::user()->name,
                'created_by' => Auth::user()->id,
            ]);

            // Alert::success('Success','Device trusted successfully');
            return redirect()->route('dashboard')
                ->with('alert.sucess','Device trusted successfully')
                ;
        }
        




    }


};
?>

<div class="flex items-center space-x-4">
    @auth
        @if($show_device_trust_section == true)
        <x-profile.button 
            buttonLabel="Trust this device?"
            buttonAction="markDeviceAsTrusted('yes')"

            confirm="yes"
            confirmationMessage="Are you sure you want to trust this device? "

            displayTooltip="true"
            tooltipText="Click to mark this device as trusted for easier sign-in"
            position="bottom"
        />
        @endif


        <!-- Notification button -->
        {{-- <button type="button" class="relative group" title="Notifications">
            <svg class="w-6 h-6 text-gray-600 group-hover:text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-5-5.917V4a2 2 0 10-4 0v1.083A6.002 6.002 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m8 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if($notificationCount > 0)
                <span class="absolute -top-1 -right-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-blue-600 px-1 text-[10px] font-bold text-white">
                    {{ $notificationCount }}
                </span>
            @endif
        </button> --}}

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


        <!-- ./ Notification button -->

        <!-- Profile (details dropdown) -->
        <details class="relative">
            <summary class="list-none cursor-pointer flex items-center gap-2 rounded-full border border-gray-200 bg-white px-2 py-1.5 hover:bg-gray-50">
                <img class="h-8 w-8 rounded-full border-2 border-blue-600" src="{{ $this->avatarUrl }}" alt="User avatar" />
                <span class="hidden sm:inline text-sm font-medium text-gray-700">{{ $user->name }}</span>
                <svg class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
                </svg>
            </summary>

            <div class="absolute right-0 mt-2 w-56 z-50 rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>

                    <div class="mt-2 flex flex-wrap gap-1">
                        @forelse($user->roles as $role)
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                {{ $role->name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400">No roles assigned</span>
                        @endforelse
                    </div>
                </div>
                <ul class="p-1 text-sm">
                    <li>
                        <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-2 rounded-md px-3 py-2 hover:bg-gray-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/><path d="M4 21a8 8 0 0 1 16 0"/>
                            </svg>
                            Profile
                        </a>
                    </li>
                    {{-- <li>
                        <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-2 rounded-md px-3 py-2 hover:bg-gray-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M12 8v8m4-4H8"/><circle cx="12" cy="12" r="9"/>
                            </svg>
                            Preferences
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('billing') }}" wire:navigate class="flex items-center gap-2 rounded-md px-3 py-2 hover:bg-gray-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M3 7h18M3 12h18M3 17h18"/><path d="M5 21h14a2 2 0 0 0 2-2v-9H3v9a2 2 0 0 0 2 2z"/>
                            </svg>
                            Billing
                        </a>
                    </li> --}}
                    <li class="border-t mt-1">
                        <button type="button" wire:click="logout" class="flex items-center gap-2 w-full rounded-md px-3 py-2 text-rose-600 hover:bg-rose-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M16 17l5-5-5-5M21 12H9"/><path d="M12 19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2"/>
                            </svg>
                            Sign out
                        </button>
                    </li>
                </ul>
            </div>
        </details>
        <!-- ./ Profile (details dropdown) -->
    @endauth

    @guest
        <div class="flex items-center justify-center gap-3 bg-white">
            <!-- Login link -->
            <a href="{{ route('login') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-full border border-blue-600 px-4 py-2 text-sm font-semibold text-blue-700 transition-all hover:bg-blue-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21H13.5a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                Login
            </a>

            <!-- Sign Up link -->
            <a href="{{ route('register') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition-all hover:bg-blue-500 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9a3 3 0 11-6 0 3 3 0 016 0zM13.5 21v-1.125a4.875 4.875 0 00-9.75 0V21M19 16v6m3-3h-6" />
                </svg>
                Sign Up
            </a>
        </div>
    @endguest
 
</div>


