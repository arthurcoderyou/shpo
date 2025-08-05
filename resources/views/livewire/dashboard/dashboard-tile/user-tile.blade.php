 
<div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-2xl hover:shadow-xl transition-shadow duration-300">
    
    <!-- Content Section -->
    <div class="p-5 flex justify-between items-center gap-x-4">
        
        <div>
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">
                {{ $title }} 
            </p>
            <div class="mt-2 flex items-center gap-x-3">
                <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    {{ $count }}
                </h3>
            </div>
        </div>

        <div class="shrink-0 flex justify-center items-center size-12 {{ $iconBg }} {{ $iconColor }} rounded-full shadow-md hover:scale-105 transform transition duration-300">
            {!! $icon !!}
        </div>

    </div>

    <!-- Footer Link -->
    <a href="{{ $route }}" 
        class="py-4 px-5 flex justify-between items-center text-sm font-medium text-gray-700 border-t border-gray-200 hover:bg-gray-100 active:bg-gray-200 focus:outline-none rounded-b-2xl transition-colors duration-200">
        View
        <svg class="size-4 ml-2 text-gray-500 group-hover:text-gray-700 transition" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="m9 18 6-6-6-6" />
        </svg>
    </a>

</div>
 
