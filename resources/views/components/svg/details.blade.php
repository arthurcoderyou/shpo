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
    <circle cx="12" cy="12" r="9" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7h.01" />
</svg>
