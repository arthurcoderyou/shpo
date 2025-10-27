@props(['title' => null])

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.5"
    aria-hidden="{{ $title ? 'false' : 'true' }}"
    role="img"
    {{ $attributes->merge(['class' => 'size-5']) }}
>
    @if($title)<title>{{ $title }}</title>@endif
    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l1 14h10l1-14" />
    <path stroke-linecap="round" d="M10 10v8M14 10v8" />
</svg>
