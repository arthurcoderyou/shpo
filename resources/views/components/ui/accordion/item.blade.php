@props([
    'id',          // item number
    'title',       // item header text
])

<div class="border border-gray-300 rounded-xl overflow-hidden bg-white shadow-sm">

    <!-- Header -->
    <button
        @click="openItem === {{ $id }} ? openItem = null : openItem = {{ $id }}"
        class="w-full flex justify-between items-center px-4 py-3 text-left 
               text-gray-800 font-medium hover:bg-gray-50"
    >
        <span>{{ $title }}</span>

        <!-- Closed Icon -->
        <svg 
            x-show="openItem !== {{ $id }}" 
            class="w-5 h-5 text-gray-500 rotate-0 transition-transform"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19 9l-7 7-7-7" />
        </svg>

        <!-- Open Icon -->
        <svg 
            x-show="openItem === {{ $id }}"
            class="w-5 h-5 text-gray-500 rotate-180 transition-transform"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Content -->
    <div 
        x-show="openItem === {{ $id }}" 
        x-collapse 
        class="px-4 pb-4 text-gray-700"
    >
        {{ $slot }}
    </div>

</div>
