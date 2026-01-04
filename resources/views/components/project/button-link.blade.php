@props([ 

    'displayTooltip' => false,
    'position' => 'top', // top, right, bottom, left
    'tooltipText' => '',
    'tooltipLabelTextArrMsg' => [],
 
    'linkLabel' => 'Label',
    'linkHref' => '#',
 
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
                        <a 
                        
                            @mouseenter="open = true"
                            @mouseleave="open = false"
                            @focus="open = true"
                            @blur="open = false"
                            @keydown.escape.window="open = false"

                            href="{{ $linkHref }}"
                            
                            class="{{ $class }}">


                            
                            <span class="hidden lg:block text-nowrap">
                                {{ $linkLabel }}
                            </span>
                            
                            <span class="block lg:hidden text-xs font-semibold text-center">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($linkLabel, 0, 2)) }}
                            </span>

                        </a>

                        

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


 
