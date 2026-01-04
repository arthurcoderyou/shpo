{{-- Single-select searchable reviewer component --}}

@props([
    'id', 
    'selected' => null,
    'name' => null,
    'label' => null,
    'required' => false,
    'disabled' => false,
    'help' => null,
    'error' => null,

    'options' => [],

   
    'entangle' => null,

    'placeholder' => 'Search by name or role…',

    'displayTooltip' => false,
    'position' => 'top', // top, right, bottom, left
    'tooltipText' => '',
    'tooltipLabelTextArrMsg' => [],

    'linkLabel' => null,
    'linkHref' => '#',
    'labelClass' => 'block text-sm font-medium text-slate-700',
    'triggerClass' => 'z-10 inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50',
])

@php
    $posClass = match ($position) {
        'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
        'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
        'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
        default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2', // top
    };
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
        <div class="flex items-center gap-2 mb-1">
            @if($linkLabel)
                <div x-data="{ open:false }" class="relative inline-flex items-center align-middle">
                    <a href="{{ $linkHref }}"
                       @mouseenter="open = true" @mouseleave="open = false"
                       @focus="open = true" @blur="open = false"
                       @keydown.escape.window="open = false"
                       class="{{ $triggerClass }}">
                        {{ $linkLabel }}
                    </a>

                    @if($displayTooltip)
                        <div x-show="open" x-cloak role="tooltip"
                             class="absolute z-50 w-56 text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1">
                            {{ $tooltipText }}
                            @if(!empty($tooltipLabelTextArrMsg))
                                <div class="my-1 space-y-1">
                                    @foreach($tooltipLabelTextArrMsg as $msgKey => $msgText)
                                        <div><span class="text-lime-500 uppercase">{{ $msgKey }}</span>: {{ $msgText }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <label for="{{ $id }}" class="{{ $labelClass }}">
                    {{ $label }} @if($required)<span class="text-red-600">*</span>@endif
                </label>
            @else
                <label for="{{ $id }}" class="{{ $labelClass }}">
                    {{ $label }} @if($required)<span class="text-red-600">*</span>@endif
                </label>

                @if($displayTooltip)
                    <div x-data="{ open:false }" class="relative">
                        <button type="button"
                                @mouseenter="open = true" @mouseleave="open = false"
                                @focus="open = true" @blur="open = false"
                                @keydown.escape.window="open = false"
                                class="inline-flex items-center rounded-md border px-2 py-1 text-xs">
                            ?
                            <span class="sr-only">Help</span>
                        </button>
                        <div x-show="open" x-cloak role="tooltip"
                             class="absolute z-50 w-56 text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1">
                            {{ $tooltipText }}
                            @if(!empty($tooltipLabelTextArrMsg))
                                <div class="my-1 space-y-1">
                                    @foreach($tooltipLabelTextArrMsg as $msgKey => $msgText)
                                        <div><span class="text-lime-500 uppercase">{{ $msgKey }}</span>: {{ $msgText }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif

    <div
        x-data="{ 
            open: false,
            search: '',
            options: @js($options), // [{id,name,roles:[]}]

            // Prefer Livewire entangle if provided, else use initial selected
            selected: @if($entangle) @entangle($entangle) @else null @endif,

             {{-- selected: @js($selected), --}}
            // coords for teleported dropdown
            dropdownStyle: '',
            computePosition() {
                this.$nextTick(() => {
                    const trigger = this.$refs.trigger;
                    if (!trigger) return;

                    const rect = trigger.getBoundingClientRect();
                    this.dropdownStyle = `
                        position: fixed;
                        left: ${rect.left}px;
                        top: ${rect.bottom + window.scrollY - 20}px;
                        width: ${rect.width}px;
                        z-index: 9999;
                    `;
                });
            },

            roleColors: {
                'global admin': 'bg-red-100 text-red-700 ring-red-200',
                'admin':        'bg-amber-100 text-amber-800 ring-amber-200',
                'reviewer':     'bg-sky-100 text-sky-700 ring-sky-200',
                'user':         'bg-slate-100 text-slate-700 ring-slate-200',
                '__none':       'bg-zinc-100 text-zinc-700 ring-zinc-200',
            },

            setSelected(id) {
                this.selected = id; 
                this.open = false;
                this.search = '';
            },

            isSelected(id) {
                return this.selected === id;
            },

            labelFor(id) {
                const o = this.options.find(o => o.id === id);
                return o ? o.name : '';
            },

            rolesFor(id) {
                const o = this.options.find(o => o.id === id);
                return o ? (o.roles ?? []) : [];
            },

            badgeCls(role) {
                const key  = (role || '').toLowerCase();
                const base = 'px-1.5 py-0.5 rounded-md text-xs ring-1';
                return `${base} ${(this.roleColors[key] ?? this.roleColors['__none'])}`;
            },

            filterList() {
                const q = this.search.trim().toLowerCase();
                if (!q) return this.options;

                return this.options.filter(o => {
                    const inName  = (o.name  || '').toLowerCase().includes(q);
                    const inRoles = (o.roles || []).some(r => (r || '').toLowerCase().includes(q));
                    return inName || inRoles;
                });
            }
        }"

        x-modelable="open"        

        class="relative mt-1"
    >

        {{-- Hidden input bound to selected --}}
        <input
            type="hidden"
            x-model="selected"
            id="{{ $id }}"
            name="{{ $name ?? $id }}"
        />


        {{-- Trigger --}}
        <button
            type="button"
            x-ref="trigger"
            @click="open = !open; if(open) computePosition()"
            @keydown.escape.window="open = false"
            :aria-expanded="open"
            class="w-full border rounded-lg px-3 py-2 flex items-center justify-between bg-white text-left
                   focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
            {{ $disabled ? 'disabled' : '' }}
        >
            <div class="flex flex-col min-w-0">
                <span class="truncate text-sm text-slate-800" x-show="selected" x-text="labelFor(selected)"></span>
                <span class="truncate text-sm text-slate-400" x-show="!selected">
                    {{ $placeholder }}
                </span>

                {{-- show roles under label --}}
                <div class="mt-1 flex flex-wrap gap-1.5" x-show="selected">
                    <template x-for="role in rolesFor(selected)" :key="role">
                        <span :class="badgeCls(role)" x-text="role"></span>
                    </template>

                    <template x-if="rolesFor(selected).length === 0">
                        <span :class="badgeCls('')">No role</span>
                    </template>
                </div>
            </div>

            <svg class="ml-2 h-4 w-4 text-slate-500 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                      clip-rule="evenodd" />
            </svg>
        </button>

        {{-- Teleported dropdown so it’s above scroll/overflow --}}
        <template x-teleport="body">
            <div
                x-show="open"
                x-transition
                x-cloak
                @keydown.escape.window="open = false"
                @click.outside="open = false"
                :style="dropdownStyle"
                class="bg-white border rounded-lg shadow max-h-64 overflow-y-auto"
            >
                {{-- Search input --}}
                <div class="p-2 border-b bg-white sticky top-0">
                    <input
                        type="text"
                        x-model="search"
                        placeholder="{{ $placeholder }}"
                        class="w-full border rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                    />
                </div>

                {{-- Options list --}}
                <template x-for="opt in filterList()" :key="opt.id">
                    <button
                        type="button"
                        @click="setSelected(opt.id)"
                        class="w-full text-left px-3 py-2 flex items-center justify-between hover:bg-indigo-50"
                    >
                        <span class="flex flex-col">
                            <span class="font-medium text-sm text-slate-800" x-text="opt.name"></span>
                            <span class="mt-1 flex flex-wrap gap-1.5">
                                <template x-for="role in (opt.roles ?? [])" :key="role">
                                    <span :class="badgeCls(role)" x-text="role"></span>
                                </template>

                                <template x-if="(opt.roles ?? []).length === 0">
                                    <span :class="badgeCls('')">No role</span>
                                </template>
                            </span>
                        </span>

                        <span
                            x-show="isSelected(opt.id)"
                            class="text-indigo-600 font-bold"
                        >
                            ✓
                        </span>
                    </button>
                </template>

                <div
                    x-show="filterList().length === 0"
                    class="px-3 py-2 text-sm text-slate-500"
                >
                    No results
                </div>
            </div>
        </template>
    </div>
</div>
