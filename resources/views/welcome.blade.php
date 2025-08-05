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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white">
        <div class="min-h-screen pt-10 sm:pt-32 ">

            <!-- #region -->



            <!-- Hero -->
            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Grid -->
                <div class="grid md:grid-cols-2 gap-4 sm:mx-4 md:gap-8 xl:gap-20 md:items-center">
                    <div>
                        <h1 class="block text-3xl font-bold text-gray-800 sm:text-4xl lg:text-6xl lg:leading-tight ">SHPO <span class="text-blue-600"> Project Portal</span></h1>
                        <p class="mt-3 text-lg text-gray-800">
                            Guam
                            <span class="text-blue-600">S</span>tate
                            <span class="text-blue-600">H</span>istoric
                            <span class="text-blue-600">P</span>reservation
                            <span class="text-blue-600">O</span>ffice
                        </p>

                        <!-- Buttons -->
                        <div class="mt-7 gap-3 w-full  inline-flex">

                            @guest
                            <a class="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" href="{{ route('login') }}" wire:navigate>
                                Login
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                            <a href="{{ route('register') }}" wire:navigate
                            class="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " href="#">
                                Signup
                            </a>
                            @endguest

                            @auth 
                                <a  class="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" 
                                href="{{ route('dashboard') }}" wire:navigate>
                                    Dashboard
                                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            @endauth
                        </div>
                        <!-- End Buttons -->


                    </div>
                    <!-- End Col -->

                    <div class="relative  sm:mx-4">
                        <img class="w-full mx-auto sm:mx-0  rounded-md" src="{{ asset('images/banner-about.jpg') }}" alt="Hero Image">

                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Grid -->
            </div>
            <!-- End Hero -->



        </div>
    </body>
</html>

