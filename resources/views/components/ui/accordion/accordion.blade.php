@props([
    'open' => null,        // default open item number
])

<div 
    {{ $attributes->merge(['class' => 'space-y-3']) }}
    x-data="{ openItem: {{ $open ? $open : 'null' }} }"
>
    {{ $slot }}
</div>