@props([ 

    'displayTooltip' => false,
    'position' => 'top', // top, right, bottom, left
    'tooltipText' => '',
    'tooltipLabelTextArrMsg' => [],
 
    'menuLabel' => 'Menu Label',

    'actions' => [],
    'class' => 'inline-flex items-center gap-1 rounded-xl border border-slate-200  px-3 py-2 text-sm font-medium text-slate-700 bg-white hover:bg-slate-50',

])

@php
    $posClass = match ($position) {
        'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
        'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
        'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
        default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2', // top
    };
@endphp

{{-- Optional global x-cloak style (only include once globally)
<style>[x-cloak]{ display:none !important; }</style>
--}}
 
    <div class="flex items-center gap-2 inline-block    "> 
         
        <div x-data="{ open: false }"
                class="relative inline-flex items-center align-middle"
                >
            <!-- Trigger -->
            <div   
                    aria-haspopup="true"
                    :aria-expanded="open">
                    
                    
                    <!-- More Menu -->
                    <div x-data="{ menuOpen:false }" class="relative" @keydown.escape="menuOpen=false" @click.outside="menuOpen=false">
                        <button 
                        
                            @mouseenter="open = true"
                            @mouseleave="open = false"
                            @focus="open = true"
                            @blur="open = false"
                            @keydown.escape.window="open = false"

                        
                            @click="menuOpen=!menuOpen"
                                class="{{ $class }}">
                            {{ $menuLabel }}

                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"/>
                            </svg>


                        </button>

                        @if(!empty($actions))
                        <div x-show="menuOpen" x-transition
                            x-cloak
                            class="absolute right-0 z-50 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                            <div class="py-1 text-sm">
                                    
                                @foreach($actions as $action)

                                    @if($action['display'])

                                        @if($action['type'] == "button")
                                            <button 
                                                wire:click="{{ $action['buttonAction'] }}"
                                                class="block w-full px-3 py-2 text-left  hover:bg-slate-50 ">
                                                {{ $action['buttonLabel'] }}
                                            </button>
                                            
                                        @elseif ($action['type'] == "link")
                                            <a href="{{ $action['linkHref'] }}"
                                            wire:navigate
                                            class="block px-3 py-2 hover:bg-slate-50">
                                                {{ $action['linkLabel'] }}
                                            </a>
                                        @endif 
                                    @endif



                                @endforeach
 

                            </div>
                        </div>
                        @endif

                    </div> 
                    
            </div>

            @if($displayTooltip )
            <!-- Tooltip panel -->
            <div x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    x-cloak
                    class="absolute z-50 w-56 h-auto text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}"
                    role="tooltip">
                {{ $tooltipText }}

                @if(!empty($tooltipLabelTextArrMsg))
                    <div class="my-1 space-y-1">
                        @foreach ($tooltipLabelTextArrMsg as $msgKey => $msgText)
                            <div>
                                <span class="text-lime-500 uppercase">{{ $msgKey }}</span>: {{ $msgText }}
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
            @endif





        </div> 
    </div> 


 
