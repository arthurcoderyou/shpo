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

    <!-- Lock Shackle -->
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M7 10V7a5 5 0 0 1 10 0v3" />

    <!-- Lock Body -->
    <rect x="5" y="10" width="14" height="11" rx="2" ry="2" />

    <!-- Keyhole -->
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 14v3" />
    <circle cx="12" cy="13" r="1" />
</svg>
