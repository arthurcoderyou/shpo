@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'options' => [],   // array of strings
    'value' => null,   // optional initial value
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div
    x-data="{
        open: false,
        search: @js(old($name, $value)),
        value: @js(old($name, $value)),
        options: @js($options),

        get filtered() {
            if (!this.search) {
                return this.options;
            }
            return this.options.filter((item) =>
                item.toLowerCase().includes(this.search.toLowerCase())
            );
        },

        select(option) {
            this.search = option;
            this.value = option;
            this.open = false;
        },
    }"
    x-on:click.away="open = false"
    class="relative w-full"
>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 mb-1">
            {{ $label }}
        </label>
    @endif

    {{-- Visible text input --}}
    <input
        id="{{ $id }}"
        type="text"
        x-model="search"
        x-on:focus="open = true"
        x-on:input="open = true"
        autocomplete="off"
        name="{{ $name }}"
        {{ $attributes->whereStartsWith('wire:model') }}
        placeholder="{{ $placeholder }}"
        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
    >

    {{-- Hidden real value (for Laravel / Livewire) --}}
    <input
        type="hidden"
        name="{{ $name }}"
        x-model="value"
        {{ $attributes->whereStartsWith('wire:model') }}
    >

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition
        class="absolute z-20 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
    >
        <template x-for="item in filtered" :key="item.rc_number + '-' + item.lot_number">
            <button
                type="button"
                x-on:click="select(item)"
                class="flex w-full flex-col items-start px-3 py-2 text-left text-sm hover:bg-slate-100"
            >
                {{-- Project name --}}
                <span class="font-semibold text-slate-900" x-text="item.name"></span>

                {{-- Meta line: RC and Lot --}}
                <span class="mt-0.5 text-xs text-slate-500">
                    <span x-text="'RC #: ' + (item.rc_number ?? 'N/A')"></span>
                    <span class="mx-1">•</span>
                    <span x-text="'Lot #: ' + (item.lot_number ?? 'N/A')"></span>
                </span>
            </button>
        </template>

        <div
            x-show="filtered.length === 0"
            class="px-3 py-2 text-sm text-slate-500"
        >
            No results found
        </div>
    </div>
</div>


{{-- -
Usage: 
<div>
    <x-input.text-dropdown
        name="company"
        label="Company"
        placeholder="Search or select company..."
        :options="$companyList"     <!-- array from controller / Livewire -->
        wire:model="company"        <!-- works in Livewire, optional -->
    />

    <x-input-error class="mt-2" :messages="$errors->get('company')" />
</div>


--}}