<div class="p-4 bg-white shadow-lg rounded-lg">

    <script src="https://unpkg.com/tributejs@5.1.3/dist/tribute.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/tributejs@5.1.3/dist/tribute.css">   

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
        id="discussion_body" 
        data-users='@json($usersForMentions)'
        
        wire:model.defer="body" required rows="4" class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-300 placeholder-gray-500" placeholder="Write your question or note..."></textarea>
        @if($errors->first('body'))
            <p id="message_body_error" class="mt-1 text-sm text-red-700">{{ $errors->first('body') }}</p>
        @endif
        
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

    <script>
        function extractMentionQuery(text, caretPos) {
            // Look backwards from caret to find the nearest "@"
            const left = text.slice(0, caretPos);
            const at = left.lastIndexOf('@');
            if (at === -1) return null;

            // Stop if there's a space/newline between @ and caret (not an active mention)
            const chunk = left.slice(at + 1);
            if (chunk.includes(' ') || chunk.includes('\n')) return null;

            return chunk; // query after "@"
        }

        function initMentions() {
            const el = document.getElementById('discussion_body');
            if (!el) return;

            // Avoid duplicates, but allow rebuilding if needed
            if (el._tribute) return;

            const initialUsers = JSON.parse(el.dataset.users || '[]');

            const tribute = new Tribute({
                trigger: '@',
                values: initialUsers,
                selectTemplate: item => '@' + item.original.value
            });

            tribute.attach(el);
            el._tribute = tribute;

            // Debounced Livewire call while typing after "@"
            let t = null;
            el.addEventListener('input', () => {
                const q = extractMentionQuery(el.value, el.selectionStart);

                // if not typing a mention, clear search (optional)
                if (q === null) return;

                clearTimeout(t);
                t = setTimeout(() => {
                    // Livewire v3: find closest component and call method
                    const comp = Livewire.find(el.closest('[wire\\:id]').getAttribute('wire:id'));
                    comp.call('setMentionSearch', q);
                }, 200);
            });

            // Listen for Livewire to push new users list
            window.addEventListener('mentions-users-updated', (e) => {
                const users = e.detail.users || [];
                // Update textarea dataset too (optional but helpful)
                el.dataset.users = JSON.stringify(users);

                // Update tribute values in-place
                el._tribute.collection[0].values = users;
            });
        }

        document.addEventListener('livewire:init', () => {
            initMentions();

            Livewire.hook('message.processed', () => {
                initMentions();
            });
        });

        document.addEventListener('livewire:navigated', () => {
            initMentions();
        });
    </script>

   
</div>
