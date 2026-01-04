@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'options' => [],   // array of objects: [{ id, name, rc_number, lot_number }]
    'value' => "",
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div
    x-data="{
        openSearch: false,
        search: '{{ $value }}',

        closeDropdown() {
            this.openSearch = false;
        }
    }"
    x-on:click.away="closeDropdown()"
    class="relative w-full"
>
    {{-- Label --}}
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 mb-1">
            {{ $label }}
        </label>
    @endif

    {{-- Search box --}}
    <input
        id="{{ $id }}"
        type="text"
        x-model="search"
        x-on:focus="openSearch = true"
        x-on:input="openSearch = true"
        x-on:blur="setTimeout(() => openSearch = false, 150)"  {{-- close after leaving input --}}
        x-on:keydown.escape.window="openSearch = false"        {{-- close on ESC --}}
        autocomplete="off"
        placeholder="{{ $placeholder }}"
        {{ $attributes->whereStartsWith('wire:model') }}
        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
    >

    {{-- Dropdown results --}}
    <div
        x-show="openSearch && search.length > 0"
        x-transition
        x-cloak
        class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
    >
        @if(!empty($options) && count($options) > 0)
            <div class="mt-0.5 text-xs text-slate-500 w-full block text-left px-3 py-2">
                <span>Related projects: </span>
            </div>

            @foreach ($options as $option)
                <a
                    href="{{ route('project.show', ['project' => $option['id']]) }}"
                    wire:navigate
                    x-on:click="closeDropdown()"
                    class="w-full block text-left px-3 py-2 hover:bg-slate-100 cursor-pointer"
                >
                    <div class="text-sm font-semibold text-slate-900">
                        RC#: {{ $option['rc_number'] ?? 'N/A' }}
                    </div>

                    <div class="mt-0.5 text-xs text-slate-500">
                        <span>Project: {{ $option['name'] }}</span>
                        <span class="mx-1">•</span>
                        <span>Location: {{ $option['location'] ?? 'N/A' }}</span>
                    </div>
                </a>
            @endforeach
        @else
            <div class="px-3 py-2 text-sm text-slate-500">
                No matching projects
            </div>
        @endif
    </div>
</div>
