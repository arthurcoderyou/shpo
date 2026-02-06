@props([
    'width' => 176,
    'buttonClass' => '',
    // options: array of [
    //   'type'  => 'link'|'button',
    //   'label' => 'Edit',
    //   'href'  => '...',              // for links
    //   'button_type' => 'button',     // for buttons
    //   'icon'  => '...svg html...',   // optional
    //   'class' => 'extra classes',    // optional
    //   'attrs' => [                   // optional extra attributes
    //      'wire:navigate' => true,
    //      'wire:click.prevent' => 'delete(1)',
    //      'onclick' => "confirm('...') || event.stopImmediatePropagation()",
    //   ]
    // ]
    'options' => [],


    'confirmModalAction' => '',
    'confirmModalTitle' => '',
    'confirmModalSubmitLabel' => '',
    'confirmModalSubmitMessage' => '',


])

<div
    x-data="{
        open: false,
        top: 0,
        left: 0,
        width: {{ (int) $width }},

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.updatePosition());
            }
        },

        updatePosition() {
            const btn = this.$refs.button;
            if (!btn) return;

            const rect = btn.getBoundingClientRect();

            // Always open to the RIGHT of the button
            this.top  = rect.top + window.scrollY;
            this.left = rect.right + window.scrollX;
        }
    }"
    class="relative"
>
    <!-- Trigger button -->
    <button
        type="button"
        x-ref="button"
        @click="toggle()"
        @keydown.escape.window="open = false"
        class="  inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-2 py-1 text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-1 {{ $buttonClass }}"
    >
        {{-- <!-- Hamburger icon -->
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
        </svg> --}}

        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="  size-4 text-gray-400">
            <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
        </svg> 
    </button>

    <!-- DROPDOWN TELEPORTED TO BODY -->
    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            x-transition
            @click.outside.window="open = false"
            :style="`
                position:absolute;
                top:${top}px;
                left:${left}px;
                width:${width}px;
            `"
            class="z-50 rounded-md bg-white shadow-lg ring-1 ring-black/5"
        >
            <div class="py-1 text-sm text-slate-700">
                @if (!empty($options))
                    @foreach ($options as $option)
                        @php
                            $type   = $option['type']  ?? 'button';
                            $label  = $option['label'] ?? '';
                            $icon   = $option['icon']  ?? null;
                            $href   = $option['href']  ?? '#';
                            $btnType = $option['button_type'] ?? 'button'; 
                            $extraClass = $option['class'] ?? '';
                            $permissions = $option['permissions'] ?? [];




                            // base style for all items  
                            $baseClass = 'flex w-full items-center gap-x-2 px-3 py-2 text-sm hover:bg-slate-100 text-slate-700 ' . $extraClass;


                            // $type  = "buttonConfirm"
                            $confirmBtnTitle = $option['confirm_btn_title'] ?? 'Confirm Button Title';
                            $confirmBtnLabel = $option['confirm_btn_label'] ?? 'Confirm Button Label'; 
                            $confirmBtnMsg = $option['confirm_btn_message'] ?? 'Are you sure you want to save this record? ';
                            $confirmBtnAction = $option['confirm_btn_action'] ?? 'save';
                            $confirmBtnBaseClass = 'flex w-full items-center gap-x-2 px-1 py-1 text-sm hover:bg-slate-100 text-slate-700 ' . $extraClass;
                            $divBaseClass = 'flex w-full items-center gap-x-2 px-3 py-1 text-sm hover:bg-slate-100 text-slate-700 ' . $extraClass;



                            // Build extra attributes
                            $attrString = '';
                            foreach (($option['attrs'] ?? []) as $attrKey => $attrVal) {
                                if (is_bool($attrVal)) {
                                    if ($attrVal) {
                                        $attrString .= ' ' . e($attrKey);
                                    }
                                } else {
                                    $attrString .= ' ' . e($attrKey) . '="' . e($attrVal) . '"';
                                }
                            }
                        @endphp

                        @php
                            $permissions = $option['permissions'] ?? [];

                            // Show if no permissions required; otherwise require at least one permission
                            $canRender = empty($permissions)
                                ? true
                                : auth()->check() && auth()->user()->canany($permissions);
                        @endphp


                        @if($canRender)
                            @if ($type === 'link')
                                <a
                                    href="{{ $href }}"
                                    class="{{ $baseClass }}"
                                    {!! $attrString !!}
                                >
                                    @if ($icon)
                                        @switch($icon)
                                            @case("show")
                                                <x-svg.details />
                                                @break 
                                            @case("edit")
                                                <x-svg.edit />
                                                @break 
                                            @case("role_edit")
                                                <x-svg.edit-role />
                                                @break 
                                            @case("delete")
                                                <x-svg.delete />
                                                @break
                                            @case("lock")
                                                <x-svg.lock />
                                                @break  
                                        
                                            @default
                                                @break;
                                        @endswitch
                                        
                                        
                                    @endif
                                    <span>{{ $label }}</span>
                                </a>
                            @elseif ($type === 'buttonConfirm')
                                <div class="{{ $divBaseClass }}">
                                    @if ($icon)
                                        @switch($icon)
                                            @case("show")
                                                <x-svg.details />
                                                @break 
                                            @case("edit")
                                                <x-svg.edit />
                                                @break 
                                            @case("role_edit")
                                                <x-svg.edit-role />
                                                @break 
                                            @case("delete")
                                                <x-svg.delete />
                                                @break
                                            @case("lock")
                                                <x-svg.lock />
                                                @break  
                                        
                                            @default
                                                
                                        @endswitch
                                        
                                        
                                    @endif

                                    <x-ui.modal.modal 
                                        permission=""
                                        :buttonLabel='$confirmBtnLabel'
                                        :modalTitle='$confirmBtnTitle' 
                                        :modalTitle='$confirmBtnTitle' 
                                        :submitBtnLabel="$confirmBtnLabel" 
                                        wireAction="{{ $confirmBtnAction }}"
                                        btnClass="{{ $confirmBtnBaseClass }}"
                                        submitBtnClass="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-lg hover:bg-sky-700" 
                                    >
                                        {{ $confirmBtnMsg }}
                                    </x-ui.modal.modal>

                                </div>
                                
                            @else
                                <button
                                    type="{{ $btnType }}"
                                    class="{{ $baseClass }}"
                                    {!! $attrString !!}
                                >
                                @if ($icon)
                                        @switch($icon)
                                            @case("show")
                                                <x-svg.details />
                                                @break 
                                            @case("edit")
                                                <x-svg.edit />
                                                @break 
                                            @case("role_edit")
                                                <x-svg.edit-role />
                                                @break 
                                            @case("delete")
                                                <x-svg.delete />
                                                @break
                                            @case("lock")
                                                <x-svg.lock />
                                                @break  
                                        
                                            @default
                                                
                                        @endswitch
                                        
                                        
                                    @endif
                                    <span>{{ $label }}</span>
                                </button>
                            @endif
                        @endif

                    @endforeach
                @else
                    {{-- Fallback: manual items via slot --}}
                    {{ $slot }}
                @endif
            </div>
        </div>
    </template>
</div>
