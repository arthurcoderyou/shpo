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
    <!-- Document shape with folded corner -->
    <path stroke-linecap="round" stroke-linejoin="round" d="M9 2h6l5 5v13a2 2 0 01-2 2H9a2 2 0 01-2-2V4a2 2 0 012-2z" />
    <polyline stroke-linecap="round" stroke-linejoin="round" points="14 2 14 8 20 8" />
</svg>
