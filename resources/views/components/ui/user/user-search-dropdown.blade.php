@props([
    'id' => '',
    'name',
    'label' => null,
    'placeholder' => '', 
    'value' => "",
    'users' => [],
    'route' => 'team.create',
    'type' => 'link',
])

@php
    $id = $attributes->get('id', $name);
@endphp





<div
    x-data="{
        openSearch: false,
        search: @js($value ?? ''),

        closeDropdown() {
            this.openSearch = false;
        },
    
        addUser(user_id,name){
        
            if(this.user_id===null ) return;
            
            @this.addUser(user_id,name);
            this.openSearch = false;

        },
         
                         


    }"
    x-on:click.away="closeDropdown()"
    class="relative w-full  "
>
    

     <div class="w-full ">
        <label class="relative block">
        {{-- <input
            type="text"
            placeholder="{{ $searchPlaceholder }}"
            @if($searchModel)
                wire:model.live.debounce.300ms="{{ $searchModel }}"
            @endif
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 pl-10 text-sm
                    text-slate-800 placeholder:text-slate-400
                    focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/60"
        > --}}


        
        {{-- Search box --}}
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="text"
            x-model="search"
            x-on:focus="openSearch = true"
            x-on:input="openSearch = true"
            x-on:blur="setTimeout(() => openSearch = false, 150)"  {{-- close after leaving input --}}
            x-on:keydown.escape.window="openSearch = false"        {{-- close on ESC --}}
            
            autocomplete="off"

            placeholder="{{ $placeholder }}"
            {{ $attributes->whereStartsWith('wire:model') }}
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 pl-10 text-sm
                    text-slate-800 placeholder:text-slate-400
                    focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/60"
        >

        {{-- Dropdown results --}}
        <div
            x-show="openSearch"
            x-transition
            x-cloak
            class="absolute z-20 mt-1 max-h-64 overflow-visible text-nowrap overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
        >
            @if(!empty($users) && count($users) > 0)
                <div class="mt-0.5 text-xs text-slate-500 w-full block text-left px-3 py-2">
                    <span>Click to select user to mention: </span>
                </div>

                @foreach ($users as $user)

                    @if($type == "link")
                        <a
                            href="{{ route($route, ['user' => $user['id']]) }}"
                            wire:navigate
                            x-on:click="closeDropdown()"
                            class="w-full block text-left px-3 py-2 hover:bg-slate-100 cursor-pointer"
                        >
                            <div class="text-sm font-semibold text-slate-900">
                                {{ $user['name'] ?? 'N/A' }}  
                            </div>
  

                        </a>
                    @elseif($type == "button")
                        @php 
                            $user_id = $user['id'];
                            $name = $user['name'];
                        @endphp
                        <button     
                            type="button"
                            @click="addUser({{ $user_id }},'{{  $name  }}' );openSearch=false"
                            class="w-full block text-left px-3 py-2 hover:bg-slate-100 cursor-pointer"
                        >
                            <div class="text-sm font-semibold text-slate-900">
                                {{ $user['name'] ?? 'N/A' }}  
                            </div>

                        </button>
                    
                    @endif
                @endforeach
            @else
                <div class="px-3 py-2 text-sm text-slate-500">
                    No matching records
                </div>
            @endif
        </div>
            









        <svg
            class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-slate-400"
            fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m21 21-3.9-3.9M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
        </svg>


        <svg 
        wire:loading wire:target="search"
        class="pointer-events-none absolute right-3 top-1.5   h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
        </svg>


    </label>
</div>

   
</div>
