<div class="p-4 bg-white shadow-lg rounded-lg">

    
    <form wire:submit="save" class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-700">
            @if (!$parent) 
                Ask a Question or Share Your Thoughts 
            @endif
        </h2>
        
        <!-- Title for questions -->
        @if (!$parent)
            <input type="text" wire:model.defer="title" placeholder="Give your question a title (e.g., Clarification needed on {{ $project->name }})" class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-300 placeholder-gray-500">
            @if($errors->first('title'))
                <p id="message_title_error" class="mt-1 text-sm text-red-700">{{ $errors->first('title') }}</p>
            @endif
        @endif
        
        <!-- Body of the question or note -->
        <textarea 
        id="body"  
        
        wire:model.defer="body" required rows="4" class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-300 placeholder-gray-500" placeholder="Write your question or note..."></textarea>
        @if($errors->first('body'))
            <p id="message_body_error" class="mt-1 text-sm text-red-700">{{ $errors->first('body') }}</p>
        @endif

        <div>
            <label for="mentions">Mentions:</label>
            <x-ui.user.user-search-dropdown :users="$users" id="search" name="search" type="button"
            wire:model.live="search" 
            />

            @if(!empty($selected_users))
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($selected_users as $user)
                        <div
                            wire:key="mention-{{ $user['user_id'] }}"
                            class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-200 px-3 py-1.5 text-sm text-blue-700 shadow-sm"
                        >
                            <!-- Mention Name -->
                            <span class="font-medium">
                                {{ '@'.$user['name'] }}
                            </span>

                            <!-- Remove Button -->
                            <button
                                type="button"
                                wire:click="removeUser({{ $user['user_id'] }})"
                                class="rounded-full p-1 hover:bg-blue-100 transition"
                            >
                                <!-- X Icon -->
                                <svg class="w-3.5 h-3.5 text-indigo-600 hover:text-red-600 transition"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
        




       

        
        <!-- Privacy Option for Admins/Reviewers -->
        @if (!$parent && auth()->user()?->hasAnyPermission(['system access global admin', 'system access admin', 'system access reviewer']))
            <div class="flex items-center space-x-2">
                <input type="checkbox" wire:model="is_private" class="text-blue-600">
                <label for="is_private" class="text-sm text-gray-600">
                    Private (only visible to Admins & Reviewers)
                </label>
            </div>
        @endif

        {{-- <div class="flex items-center space-x-2">
            <input type="checkbox" wire:model="notify_email" class="text-blue-600">
            <label for="notify_email" class="text-sm text-gray-600">
                Notify email
            </label>
        </div> --}}

        <!-- Submit Button -->
        <div class="flex justify-between items-center">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition duration-200">Post</button>
        </div>
    </form> 

   
</div>
