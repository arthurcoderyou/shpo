<div>
    <!-- Subscriber section --> 
        <x-ui.user.subscriber-section
            :users="$users"
            :selectedUsers="$selectedUsers"
            query="query"
            removeAction="removeSubscriber"
        />
    <!-- ./ Subscriber section --> 
    <div class="mt-1.5 text-right">
        <x-ui.button
            type="button"
            sr="Submit Subscribers"
            label="Save"
            labelClass=""
            x-on:click="
                if (confirm('Are you sure you want to save this subscribers list?')) {
                    $wire.save();
                    openSubscribers = false;
                }
            "
            >
                 
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 13l4 4L19 7" />
                </svg>

            </x-ui.button>

    </div>
     

    {{-- Do not remove --}}
    {{-- 
        Essential for getting the model id from the browser bar 
        This is to get model id for : 
        1. Full page load (hard refresh, direct URL, normal navigation)
        2. Livewire SPA navigation (wire:navigate)
    --}}
    @push('scripts')
        <script>

            (function () {

                function getData(){
                    window.pageProjectId = @json(optional(request()->route('project'))->id ?? request()->route('project') ?? null);
                    console.log(window.pageProjectId);

                    window.pageProjectDocumentId = @json(optional(request()->route('project_document'))->id ?? request()->route('project_document') ?? null);
                    console.log(window.pageProjectDocumentId);
                    

                    const pageProjectId = window.pageProjectId; // can be null
                    const pageProjectDocumentId = window.pageProjectDocumentId; // can be null

                    //  Listener for the project discussion events
                    if (pageProjectId) {
                        console.log(`listening to : ${pageProjectId}`);
                        window.Echo.private(`project.project_subscriber.${pageProjectId}`)
                            .listen('.event', (e) => {
                                console.log('[project model-scoped]');

                                let dispatchEvent = `projectSubscriberEvent.${pageProjectId}`;
                                Livewire.dispatch(dispatchEvent); 

                                console.log(dispatchEvent); 

                            });
                    }

                    // Listener for the project document discussion events
                    if (pageProjectDocumentId) {
                        console.log(`listening to : ${pageProjectDocumentId}`);
                        window.Echo.private(`project.project_document.project_subscriber.${pageProjectDocumentId}`)
                            .listen('.event', (e) => {
                                console.log('[project model-scoped]');

                                let dispatchEvent = `projectDocumentSubscriberEvent.${pageProjectDocumentId}`;
                                Livewire.dispatch(dispatchEvent); 

                                console.log(dispatchEvent); 

                            });
                    }






                }

                /**
                 * 1. Full page load (hard refresh, direct URL, normal navigation)
                 */
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        getData();
                    });
                } else {
                    // DOM already loaded
                    getData();
                }

                /**
                 * 2. Livewire SPA navigation (wire:navigate)
                 */
                document.addEventListener('livewire:navigated', () => {
                    getData();
                });







            })();
 


        </script>
    @endpush
</div>
