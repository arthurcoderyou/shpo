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
    @if($title)
        <title>{{ $title }}</title>
    @endif
    <!-- Reviewer icon: user + check -->
    <path stroke-linecap="round" stroke-linejoin="round"
        d="M15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.5 20.25a8.25 8.25 0 0 1 15-4.5m0 0 2.25 2.25M19.5 15.75l2.25-2.25" />
</svg>
