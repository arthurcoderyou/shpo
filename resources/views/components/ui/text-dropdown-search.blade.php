
@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'options' => [],
    'itemType' => 'link',     // 'link' or 'button'
    'itemUrl'  => null,       // optional custom URL pattern for links
    'itemAction' => null,     // JS method to trigger for button mode
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div
    x-data="{
        open: false,
        search: '',
        options: @js($options),

        get filtered() {
            if (!this.search) {
                return this.options;
            }

            const term = this.search.toLowerCase();
            return this.options.filter((item) => {
                const name  = (item.name ?? '').toLowerCase();
                const rc    = (item.rc_number ?? '').toLowerCase();
                const lot   = (item.lot_number ?? '').toLowerCase();

                return name.includes(term) || rc.includes(term) || lot.includes(term);
            });
        },

        buildUrl(item) {
            // If component user supplies a custom URL pattern
            if ('{{ $itemUrl }}') {
                return '{{ $itemUrl }}'.replace(':id', item.id);
            }

            // Default
            return `/project/${item.id}/show`;
        },
    }"
    x-on:click.away="open = false"
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
        x-on:focus="open = true"
        x-on:input="open = true"
        autocomplete="off"
        placeholder="{{ $placeholder }}"
        {{ $attributes->whereStartsWith('wire:model') }}
        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
    >

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition
        class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
    >
        <template x-for="item in filtered" :key="item.id">

            {{-- Case 1: LINK --}}
            <template x-if="'{{ $itemType }}' === 'link'">
                <a
                    :href="buildUrl(item)"
                    class="block px-3 py-2 hover:bg-slate-100 cursor-pointer"
                >
                    <div class="text-sm font-semibold text-slate-900" x-text="item.name"></div>

                    <div class="mt-0.5 text-xs text-slate-500">
                        <span x-text="'RC #: ' + (item.rc_number ?? 'N/A')"></span>
                        <span class="mx-1">•</span>
                        <span x-text="'Lot #: ' + (item.lot_number ?? 'N/A')"></span>
                    </div>
                </a>
            </template>

            {{-- Case 2: BUTTON --}}
            <template x-if="'{{ $itemType }}' === 'button'">
                <button
                    type="button"
                    @click="{{ $itemAction ? $itemAction . '(item)' : '' }}"
                    class="w-full text-left px-3 py-2 hover:bg-slate-100 cursor-pointer"
                >
                    <div class="text-sm font-semibold text-slate-900" x-text="item.name"></div>

                    <div class="mt-0.5 text-xs text-slate-500">
                        <span x-text="'RC #: ' + (item.rc_number ?? 'N/A')"></span>
                        <span class="mx-1">•</span>
                        <span x-text="'Lot #: ' + (item.lot_number ?? 'N/A')"></span>
                    </div>
                </button>
            </template>

        </template>

        <div
            x-show="filtered.length === 0"
            class="px-3 py-2 text-sm text-slate-500"
        >
            No matching projects
        </div>
    </div>
</div>
