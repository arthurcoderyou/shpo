<div id="notification-panel" class="hs-overlay hs-overlay-open:translate-x-0 hidden -translate-x-full fixed top-0 start-0 transition-all duration-300 transform h-full max-w-xs md:max-w-[85rem] w-full z-[9999] bg-white text-gray-800 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:text-gray-800 focus:bg-gray-100 " role="dialog" tabindex="-1" aria-labelledby="notification-panel-label">
  <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 ">
    <h3 id="notification-panel-label" class="font-bold text-gray-800 ">
      Notifications
    </h3>
    <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none " 
    aria-label="Close" 
    data-hs-overlay="#notification-panel">
      <span class="sr-only">Close</span>
      <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 6 6 18"></path>
        <path d="m6 6 12 12"></path>
      </svg>
    </button>
  </div>
  <div class="p-1">
    



      <livewire:notification.notification-list />







  </div>
</div>