<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="icon" type="image/png" href="{{ asset('images/GEPA_LOGO.png') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/livewire-dropzone/livewire-dropzone.css') }}"> 

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>


        {{-- <!-- The callback parameter is required, so we use console.debug as a noop -->
        <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA70BOfcc1ELmwAEmY-rFNkbNauIXT79cA&callback=console.debug&libraries=maps,marker&v=beta">
        </script>

       

        <style>
            /* Always set the map height explicitly to define the size of the div
            * element that contains the map. */
            gmp-map {
                height: 100%;
            }

            /* Optional: Makes the sample page fill the window. */
            html,
            body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
        </style> --}}



        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white">
        <div class="min-h-screen  ">

            @auth
                <livewire:layout.navigation />
            @endauth
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow sm:hidden">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif


            


            <!-- Page Content -->

            <main class=" ">
                {{ $slot }}
            </main>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                if (window.Livewire) {
                    window.Livewire.restart();
                }
            });
        </script>

        @include('sweetalert::alert')
        <!-- Push custom scripts from views -->
        @stack('scripts')  <!-- This will include any scripts pushed to the stack -->

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let pdfUrl = @json(session('pdf_url'));  // Retrieve the session PDF URL
                if (pdfUrl) {
                    window.open(pdfUrl, '_blank');  // Open the PDF in a new tab
                    @php session()->forget('pdf_url'); @endphp  // Clear the session after use
                }
            });
        </script>

        
    </body>
</html>
