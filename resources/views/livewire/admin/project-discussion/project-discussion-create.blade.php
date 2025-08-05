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
        @endif
        
        <!-- Body of the question or note -->
        <textarea wire:model.defer="body" required rows="4" class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-300 placeholder-gray-500" placeholder="Write your question or note..."></textarea>
        
        <!-- Privacy Option for Admins/Reviewers -->
        @if (!$parent && auth()->user()?->hasAnyPermission(['system access global admin', 'system access user', 'system access reviewer']))
            <div class="flex items-center space-x-2">
                <input type="checkbox" wire:model="is_private" class="text-blue-600">
                <label for="is_private" class="text-sm text-gray-600">
                    Private (only visible to Admins & Reviewers)
                </label>
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex justify-between items-center">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition duration-200">Post</button>
        </div>
    </form>


   
</div>
