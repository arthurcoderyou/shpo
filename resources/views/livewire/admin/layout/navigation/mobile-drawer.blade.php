<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <div id="backdrop" class="lg:hidden fixed inset-0 bg-black/50 hidden z-40" onclick="toggleSidebar(false)"></div>
    <aside id="mobile-drawer" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white border-r z-50 translate-x-[-100%] transition-transform duration-200 ease-out">

        <!-- Brand --> 
        {{-- Mobile drawer brand --}}
        <livewire:admin.layout.navigation.brand
            variant="mobile"
            name="Your App"
            subtitle="Admin Portal"
            badgeClass="bg-indigo-600 text-white"
            :use-auth-brand="true"
            mobileCloseAction="toggleSidebar(false)"  {{-- optional hook --}}
        />





        {{-- Mobile (inside your drawer) --}}
        <livewire:admin.layout.navigation.nav variant="mobile" />




    </aside>
</div>
