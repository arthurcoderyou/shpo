{{-- -
Sample usage

<div wire:key="type-{{ $typeId }}" class="grid grid-cols-12 gap-3 items-end mb-2">
    <div class="col-span-12 md:col-span-4">
        <x-reviewer-multi-select
            :options="$options"
            :entangle="'selectedByType.' . $typeId"
            :label="'Select reviewers (' . ($documentTypes[array_search($typeId, array_column($documentTypes,'id'))]['name'] ?? '') . ')'"
        />
    </div>
</div>


// Livewire property path, e.g. "selectedByType.{{ $typeId }}"

--}}



@props([
    'id',
    'name' => null,
    'label' => null,
    'required' => false,
    'disabled' => false, 
    'help' => null,
    'error' => null,
    'options' => [],            // ['value' => 'Label', ...]
        
    'entangle' => null,
    'placeholder' => 'Search by name or role…',

    // Tooltip (optional)
    'displayTooltip' => false,
    'position' => 'top',
    'tooltipText' => '',
    'tooltipLabelTextArrMsg' => [],


    'linkLabel' => null,
    'linkHref' => '#',
    'labelClass' => 'block text-sm font-medium text-slate-700',
    'triggerClass' => 'inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50',
    
])

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
            <label for="{{ $id }}" class="{{ $labelClass }}">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</label>
        @else
            <label for="{{ $id }}" class="{{ $labelClass }}">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</label>
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
            open:false,   // dropdown open/closed state
            search:'',    // search filter
            options:@js($options),    // [{id,name,roles:[]}]
            selected: @if($entangle) @entangle($entangle) @else [] @endif,

            roleColors: {       // role colors 
                'global admin': 'bg-red-100 text-red-700 ring-red-200',
                'admin':        'bg-amber-100 text-amber-800 ring-amber-200',
                'reviewer':     'bg-sky-100 text-sky-700 ring-sky-200',
                'user':         'bg-slate-100 text-slate-700 ring-slate-200',
                '__none':       'bg-zinc-100 text-zinc-700 ring-zinc-200', // no role
            },

            toggle(id){
                this.isSelected(id) ? this.remove(id) : this.add(id)
            },

            add(id){
                if(!this.isSelected(id)) this.selected.push(id)
            },

            remove(id){
                this.selected = (this.selected ?? []).filter(v => v !== id)
            },

            isSelected(id){
                return (this.selected ?? []).includes(id)
            },

            labelFor(id){
                const o = this.options.find(o => o.id === id);
                return o ? o.name : id;
            },

            rolesFor(id){
                const o = this.options.find(o => o.id === id);
                return o ? (o.roles ?? []) : [];
            },

            badgeCls(role){
                const key  = (role || '').toLowerCase();
                const base = 'px-1.5 py-0.5 rounded-md text-xs ring-1';
                return `${base} ${(this.roleColors[key] ?? this.roleColors['__none'])}`;
            },

            filterList(){
                const q = this.search.trim().toLowerCase();
                if (!q) return this.options;

                return this.options.filter(o => {
                const inName  = (o.name  || '').toLowerCase().includes(q);
                const inRoles = (o.roles || []).some(r => (r || '').toLowerCase().includes(q));
                return inName || inRoles;
                });
            }
        }"
        class="relative mt-1"
    >
        <!-- Trigger -->
        <div
          @click="open = !open"
          class="border rounded-lg px-2 py-1 flex flex-wrap items-center gap-2 bg-white focus-within:ring-2 focus-within:ring-sky-500 min-h-[44px] cursor-text"
        >
          <!-- Selected chips -->
          <template x-for="id in (selected ?? [])" :key="id">
            <span class="bg-indigo-100 text-indigo-700 text-sm px-2 py-1 rounded-full flex items-center gap-2">
              <span class="flex items-center gap-2">
                <span x-text="labelFor(id)"></span>

                <!-- role badges inside chip -->
                <template x-for="role in rolesFor(id)" :key="role">
                  <span :class="badgeCls(role)" x-text="role"></span>
                </template>

                <!-- no role -->
                <template x-if="rolesFor(id).length === 0">
                  <span :class="badgeCls('')">No role</span>
                </template>
              </span>

              <button
                type="button"
                @click.stop="remove(id)"
                class="leading-none text-slate-500 hover:text-slate-700"
              >
                &times;
              </button>
            </span>
          </template>

          <!-- search input -->
          <input
            type="text"
            x-model="search"
            :placeholder="'{{ $placeholder }}'"
            class="flex-grow border-0 focus:ring-0 text-sm text-gray-700 outline-none min-w-[80px] px-2 py-1 "
          />
        </div>

            <!-- Dropdown -->
            <div
            x-show="open"
            x-transition
            @click.outside="open=false"
            x-cloak
            class="absolute left-0 right-0 z-50 mt-1 bg-white border rounded-lg shadow max-h-60 overflow-y-auto"
            >
            <template x-for="opt in filterList()" :key="opt.id">
                <button
                type="button"
                @click="toggle(opt.id)"
                class="w-full text-left px-3 py-2 flex items-center justify-between hover:bg-indigo-50"
                >
                <span class="flex flex-col">
                    <span class="font-medium" x-text="opt.name"></span>
                    <span class="mt-1 flex flex-wrap gap-1.5">
                    <!-- role badges -->
                    <template x-for="role in (opt.roles ?? [])" :key="role">
                        <span :class="badgeCls(role)" x-text="role"></span>
                    </template>

                    <!-- no role -->
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
    </div>
</div>
