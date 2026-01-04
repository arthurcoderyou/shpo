Below is a **clean, extensible modification** of your Laravel Blade component that allows you to switch the dropdown item between:

1. **A link** (`<a href="...">...</a>`)
2. **A button** (`<button @click="...">...</button>`)

This is handled through **component props**:

### New Props Added

```php
'itemType' => 'link',  // 'link' or 'button'
'itemUrl'  => null,    // when using link
'itemAction' => null,  // JavaScript function name or Alpine expression if using button
```

---

# Updated Component Code (with dynamic link OR button behavior)

```blade
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
```

---

# How to Use It

## 1. **Use as link dropdown (default)**

```blade
<x-project-search
    name="project"
    label="Search Project"
    :options="$projects"
    itemType="link"
/>
```

### Optional: custom link pattern

```blade
<x-project-search
    name="project"
    :options="$projects"
    itemType="link"
    itemUrl="/project/:id/details"
>
```

`:id` will automatically be replaced with the item ID.

---

## 2. **Use as button dropdown**

For example, triggering a Livewire method or Alpine function.

```blade
<x-project-search
    name="project"
    :options="$projects"
    itemType="button"
    itemAction="selectProject"   {{-- Alpine method --}}
/>
```

Your Alpine parent must have:

```html
<div x-data="{ selectProject(item) { console.log(item); } }">
```

Or in Livewire:

```html
<x-project-search
    name="project"
    :options="$projects"
    itemType="button"
    itemAction="$wire.selectProject"
/>
```

Now each dropdown item becomes:

```
<button @click="$wire.selectProject(item)">...</button>
```

---

# Outcome

You now have:

### ✔ Fully configurable behavior

### ✔ Choose between hyperlinks or action buttons

### ✔ Custom URL generation

### ✔ Custom JavaScript / Alpine / Livewire click actions

### ✔ Clean and reusable component

---

If you want:

• support for **icon rendering**,
• support for **multiple button types** (primary, danger, etc.),
• support for **passing custom blade partial templates per item**,
• support for **keyboard navigation (↑↓↵)**,

Just tell me and I will enhance it.
