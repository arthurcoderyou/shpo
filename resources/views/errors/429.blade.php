<x-app-layout>

    <!-- Table Section -->
    <div class="max-w-full px-4 py-6 sm:px-6 lg:px-8  mx-auto">
     
        <!-- Card -->
        <div class=""> 
            <div class="p-1.5 min-w-fit mx-auto block align-middle">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden ">
                <!-- Header -->
                <div class="px-3 py-2    border-b border-gray-200 ">
                      
    
                    <!-- ========== HEADER ========== -->
                    <header class="mb-auto flex justify-center z-50 w-full py-4">
                        <nav class="px-4 sm:px-6 lg:px-8">
                            <a class="flex-none font-semibold text-xl text-black focus:outline-none focus:opacity-80 dark:text-white" href="#" aria-label="Brand">
                                <input type="image" src="{{ asset('images/logo-ghrd.png') }}" class="max-w-52 sm:max-w-72" alt="">
                            </a>
                        </nav>
                    </header>
                    <!-- ========== END HEADER ========== -->
                    
                    <!-- ========== MAIN CONTENT ========== -->
                    <main id="content">
                        <div class="text-center py-10 px-4 sm:px-6 lg:px-8">
                        <h1 class="block text-5xl font-bold text-gray-800 sm:text-9xl ">
                            429 - Too Many Requests
                        </h1>
                        <p class="mt-3 text-gray-600 ">
                            🐢 Whoa! You’re making too many requests.
                        </p>
                        <p class="text-gray-600 ">
                            🕐 Please slow down and try again in a moment.
                        </p>
                        <div class="mt-5 flex flex-col justify-center items-center gap-2 sm:flex-row sm:gap-3">
                            <a href="{{ route('login') }}" class="w-fit sm:w-auto py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" href="../examples.html">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            Login
                            </a>
                        </div>
                        </div>
                    </main>
                    <!-- ========== END MAIN CONTENT ========== -->
                    
                    <!-- ========== FOOTER ========== -->
                    <footer class="mt-auto text-center py-5">
                        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                        <p class="text-sm text-gray-500 ">©Copyright {{ date('Y') }} | All Rights Reserved | Dimension Systems, Inc.  </p>
                        </div>
                    </footer>
                    <!-- ========== END FOOTER ========== -->
    
                </div>
                </div>
            </div> 
        </div>
    
    
    </x-app-layout>
    