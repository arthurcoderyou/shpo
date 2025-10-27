<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="hidden lg:flex items-center justify-between px-6 py-3 border-b bg-white sticky top-0 z-40">

    {{-- Desktop bar with logo + notifications --}}
    <livewire:admin.layout.navigation.topbar.profile-bar variant="desktop" :showLogo="true" :showNotifications="true" :notificationCount="$unreadCount ?? 0" />




</div>
