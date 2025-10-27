<?php

use Livewire\Volt\Component;

new class extends Component {
    
}; ?>

<header class="lg:hidden flex items-center justify-between gap-2 px-4 py-3 border-b bg-white sticky top-0 z-40">
    <button aria-label="Open navigation" class="inline-flex items-center justify-center rounded-xl border px-3 py-2 hover:bg-gray-50 active:scale-95 transition" onclick="toggleSidebar(true)">
    <!-- Hamburger -->
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
    </button>


    {{-- Desktop bar with logo + notifications --}}
    <livewire:admin.layout.navigation.topbar.profile-bar variant="mobile" :showLogo="true" :showNotifications="true" :notificationCount="$unreadCount ?? 0" />


 
</header>
