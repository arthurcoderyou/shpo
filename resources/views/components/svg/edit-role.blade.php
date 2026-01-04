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

    <!-- User circle -->
    <path stroke-linecap="round" stroke-linejoin="round"
        d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0z" />

    <!-- User body -->
    <path stroke-linecap="round" stroke-linejoin="round"
        d="M4.5 20.25a7.5 7.5 0 0 1 15 0" />

    <!-- Pencil (edit) -->
    <path stroke-linecap="round" stroke-linejoin="round"
        d="m18 14 2.25 2.25M15.75 16.25l-.56 2.78a.75.75 0 0 0 .91.91l2.78-.56L21 16.5a.75.75 0 0 0 0-1.06L18.56 13a.75.75 0 0 0-1.06 0l-1.75 1.75z" />
</svg>
