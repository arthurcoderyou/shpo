@props(['discussion', 'depth' => 0])

@php
    $childDepth = $depth + 1;
    $marginLeft = $depth * 1.5; // Adjust indent per level here
@endphp

<div class="mb-4" style="margin-left: {{ $marginLeft }}rem;">
    <div class="p-4 border rounded-lg shadow-lg bg-white w-full @if($discussion->parent_id) border-l-4 border-blue-300 @endif">
        
        <div class="flex justify-between items-center mb-2">
            <div class="flex items-center space-x-2">
                <div class="text-sm text-gray-700 font-semibold">{{ $discussion->creator->name }}</div>
                <div class="text-xs text-gray-500">{{ $discussion->created_at->diffForHumans() }}</div>
            </div>
            @if($discussion->is_private)
                <span class="text-xs bg-yellow-300 text-yellow-800 px-2 py-0.5 rounded">Private</span>
            @endif
        </div>

        {{-- Parent discussion information --}}
        @if($discussion->parent && $depth > 0)
            <div class="mb-2 p-3 border-l-4 border-blue-400 bg-blue-50 text-xs text-gray-700 rounded-md shadow-sm">
                <div class="flex items-start space-x-2">
                    <div class="font-semibold text-blue-600">{{ $discussion->parent->creator->name }}</div>
                    <div class="text-gray-500 text-xs italic">{{ $discussion->parent->created_at->diffForHumans() }}</div>
                </div>
                <div class="mt-2 p-2 bg-white border rounded-lg shadow-sm text-sm">
                    <blockquote class="italic text-gray-600">
                        "{{ \Illuminate\Support\Str::limit(strip_tags($discussion->parent->body), 150) }}"
                    </blockquote>
                </div>
            </div>
        @endif

        {{-- Discussion title --}}
        @if($discussion->title)
            <h3 class="text-md font-semibold text-gray-800 mb-1">{{ $discussion->title }}</h3>
        @endif

        {{-- Main discussion body --}}
        <p class="text-sm text-gray-800">{{ $discussion->body }}</p>

        {{-- Reply action button --}}
        <div class="mt-2 flex items-center justify-between">
            @if($discussion->project->canPostInDiscussion())
                <div>
                    <button
                        class="text-xs text-blue-600 hover:underline"
                        wire:click="startReply({{ $discussion->id }})"
                    >
                        Reply
                    </button>
                </div>

                @if(auth()->user()->id == $discussion->created_by)
                    <div>
                        <button
                            class="text-xs text-blue-600 hover:underline"
                            wire:click="startEdit({{ $discussion->id }})"
                        >
                            Edit
                        </button>

                        {{-- <button
                            class="text-xs text-red-600 hover:underline"
                             onclick="confirm('Are you sure you want to delete this message? Deleting will also delete the replies as well') || event.stopImmediatePropagation()"
                            wire:click.prevent="delete_discussion({{ $discussion->id }})"
                        >
                            Delete
                        </button> --}}
                    </div>
                @endif
                
            @endif
        </div>
    </div>

    {{-- Render replies recursively --}}
    @if($discussion->replies)
        <div class="mt-2 space-y-4">
            @foreach($discussion->replies as $reply)
                <x-project.discussion-thread :discussion="$reply" :depth="$childDepth" />
            @endforeach
        </div>
    @endif
</div>
