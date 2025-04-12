<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
    
    {{-- @if($project->canPostInDiscussion() && $replyToId) --}}
        @if(!auth()->user()->hasRole('User'))
            <div class="flex justify-end mb-4">
                <fieldset class="flex space-x-4 text-sm text-gray-700">
                    <label class="flex items-center space-x-2">
                        <input type="radio" wire:model.live="discussionVisibility" value="all"
                            class="form-radio text-blue-600">
                        <span>All</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="radio" wire:model.live="discussionVisibility" value="private"
                            class="form-radio text-blue-600">
                        <span>Private Only</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="radio" wire:model.live="discussionVisibility" value="public"
                            class="form-radio text-blue-600">
                        <span>Public Only</span>
                    </label>
                </fieldset>
            </div>
        @endif

    {{-- @endif --}}
    
    {{-- Discussion Threads --}}
    <div class="pb-32">
        @if(!empty($discussions))
            <div class="space-y-4">
                @foreach ($discussions as $discussion)
                    <x-project.discussion-thread :discussion="$discussion" />
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-500 py-12">
                <p>No discussions yet. Be the first to start the conversation!</p>
            </div>
        @endif
    </div>

    {{-- Reply Box --}}
    @if($project->canPostInDiscussion() && $replyToId)
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t z-50 shadow-lg">
            <div class="max-w-4xl mx-auto px-4 py-5">
                <div class="mb-3 flex justify-between items-center">
                    <h3 class="text-md font-medium text-gray-800">
                        Replying to <span class="text-blue-600 font-semibold">{{ \App\Models\ProjectDiscussion::find($replyToId)?->creator->name }}</span>
                    </h3>
                    <button wire:click="cancelReply" class="text-gray-500 text-sm hover:underline">Cancel</button>
                </div>

                <form wire:submit="submitReply" >
                    {{-- <!-- Title for questions -->
                    @if (!$editParentId)
                        <input type="text" wire:model.defer="title" placeholder="Give your question a title (e.g., Clarification needed on {{ $project->name }})" class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-300 placeholder-gray-500">
                    @endif --}}

                    <textarea
                        wire:model.defer="replyBody"
                        autofocus
                        rows="4"
                        class="w-full border rounded-lg p-3 text-sm resize-none placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Write your reply..."></textarea>

                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-300">
                            Post Reply
                        </button>

                         
                    </div>
                </form>
            </div>
        </div>
    @endif


    {{-- Edit Box --}}
    @if($project->canPostInDiscussion() && $editId)
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t z-50 shadow-lg">
            <div class="max-w-4xl mx-auto px-4 py-5">
                <div class="mb-3 flex justify-between items-center">
                    <h3 class="text-md font-medium text-gray-800">
                        Editing Discussion 
                        {{-- by <span class="text-blue-600 font-semibold">{{ \App\Models\ProjectDiscussion::find($editId)?->creator->name }}</span> --}}
                    </h3>
                    <button wire:click="cancelUpdate" class="text-gray-500 text-sm hover:underline">Cancel</button>
                </div>

                <form wire:submit="submitUpdate" class="space-y-4">


                     <!-- Title for questions -->
                    @if (!$editParentId)
                        <input type="text" wire:model.defer="editTitle" placeholder="Give your question a title (e.g., Clarification needed on {{ $project->name }})" class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-300 placeholder-gray-500">
                    @endif

                    <textarea
                        wire:model.defer="editBody"
                        autofocus
                        rows="4"
                        class="w-full border rounded-lg p-3 text-sm resize-none placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Write your reply..."></textarea>

                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-300">
                            Update
                        </button>

                         
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
