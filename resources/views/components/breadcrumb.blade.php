<div class=" px-4 pt-6 pb-2 sm:px-6 lg:px-8  mx-auto  ">
 
    <!-- resources/views/components/breadcrumb.blade.php -->
    <nav class="flex text-sm text-slate-600  " aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            @foreach ($items as $item)
                <li class="inline-flex items-center">
                    @if (!$loop->last)
                        <a href="{{ $item['url'] }}" wire:navigate
                        class="inline-flex items-center text-slate-500 hover:text-slate-700">
                            @if(isset($item['icon']))
                                <x-dynamic-component :component="'icons.' . $item['icon']" class="mr-1"/>
                            @endif
                            {{ $item['label'] }}
                        </a>
                        <svg class="w-3 h-3 text-slate-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5l7 7-7 7"/>
                        </svg>
                    @else
                        <span class="inline-flex items-center font-semibold text-slate-700">
                            @if(isset($item['icon']))
                                <x-dynamic-component :component="'icons.' . $item['icon']" class="mr-1"/>
                            @endif
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>
