@props(['title' => null, 'class' => ''])

<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 448 512" {{ $attributes->merge(['class' => $class]) }}>
    @if($title)
        <title>{{ $title }}</title>
    @endif
    <path d="M144 144v48H304V144a80 80 0 1 0-160 0zM96 192V144a128 128 0 1 1 256 0v48h16c17.7 0 32 14.3 32 32V464c0 
    17.7-14.3 32-32 32H80c-17.7 0-32-14.3-32-32V224c0-17.7 14.3-32 32-32h16z"/>
</svg>
