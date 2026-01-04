@props(['title' => null])

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.5"
    role="img"
    aria-hidden="{{ $title ? 'false' : 'true' }}"
    {{ $attributes->merge(['class' => 'size-5']) }}
>
    @if($title)
        <title>{{ $title }}</title>
    @endif

    <!-- Circle -->
    <circle cx="12" cy="12" r="9" />

    <!-- Plus -->
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" />
</svg>
