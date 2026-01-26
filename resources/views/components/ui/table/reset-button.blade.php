@props([
    'wireClick' => 'resetFilters',
    'disabled' => false,
])

<button
    type="button"
    wire:click="{{ $wireClick }}"
    wire:loading.attr="disabled"
    {{ $attributes->merge([
        'class' => '
            py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg
            border border-blue-200 bg-blue-500 text-white shadow-sm
            hover:bg-blue-50 hover:text-blue-600 hover:border-blue-500
            focus:outline-blue-500 focus:text-blue-500 focus:bg-blue-50
            disabled:opacity-50 disabled:pointer-events-none
        '
    ]) }}
>
    <svg
        class="shrink-0 size-4"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M21 12a9 9 0 1 1-3-6.7"/>
        <polyline points="21 3 21 9 15 9"/>
    </svg>

    {{ $slot }}
</button>
