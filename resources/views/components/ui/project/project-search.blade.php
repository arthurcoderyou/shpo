
@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'options' => [],
    'itemType' => 'link',     // 'link' or 'button'
    'itemUrl'  => null,       // optional custom URL pattern for links
    'itemAction' => null,     // JS method to trigger for button mode, 
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div
    x-data="{
        openSearch: false,
        search: '', 
        
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
        x-on:focus="if (search.trim().length > 0) openSearch = true"
        x-on:input="openSearch = search.trim().length > 0"
        x-on:blur="setTimeout(() => openSearch = false, 150)"  {{-- close after leaving input --}}
        x-on:keydown.escape.window="openSearch = false"        {{-- close on ESC --}}
        autocomplete="off"
        placeholder="{{ $placeholder }}"
        {{ $attributes->whereStartsWith('wire:model') }}
        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
    >

    {{-- Dropdown --}}
    <div
        x-show="openSearch && search.trim().length > 0"
        x-transition
        x-cloak
        class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
    >
        
        @if(!empty($options) && count($options) > 0)

            @foreach ($options as $option) 
                <button
                    type="button"
                    wire:click="addProjectReference({{ $option['id'] }})"
                    x-on:click="openSearch = false"  
                    class="w-full text-left px-3 py-2 hover:bg-slate-100 cursor-pointer"
                >
                    <div class="text-sm font-semibold text-slate-900" >{{ $option['name'] }}</div>

                    <div class="mt-0.5 text-xs text-slate-500">
                        <span>RC#: {{ $option['rc_number'] ?? 'N/A' }}</span>
                        <span class="mx-1">•</span>
                        <span>Location: {{ $option['location'] ?? 'N/A' }}</span>
                    </div>
                </button> 

            @endforeach
        @else

            <div 
                class="px-3 py-2 text-sm text-slate-500"
            >
                No matching users
            </div>
        @endif
    </div>
</div>
