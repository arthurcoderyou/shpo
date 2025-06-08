@props(['type'])

@php
    $reviewerInfo = [
        'initial' => [
            'title' => 'Initial Reviewer',
            'description' => 'Initial reviewers are required to review the project before document review can begin.',
            'icon' => '🕵️‍♂️'
        ],
        'document' => [
            'title' => 'Document Reviewer',
            'description' => 'Document reviewers are responsible for reviewing specific project documents assigned to them.',
            'icon' => '📄'
        ],
        'final' => [
            'title' => 'Final Reviewer',
            'description' => 'Final reviewers are responsible for reviewing the entire project after all document reviews have been completed.',
            'icon' => '✅'
        ],
    ];
@endphp

@if(isset($reviewerInfo[$type]))
    <div class="space-y-2 col-span-12 bg-gray-50 border border-gray-200 rounded-lg p-4 mt-1.5">
        <div class="flex items-start space-x-2">
            <div class="text-xl">{{ $reviewerInfo[$type]['icon'] }}</div>
            <div>
                <h4 class="font-semibold text-gray-800">{{ $reviewerInfo[$type]['title'] }}</h4>
                <p class="text-sm text-gray-700 mt-1">
                    {{ $reviewerInfo[$type]['description'] }}
                </p>
            </div>
        </div>
    </div>
@endif
