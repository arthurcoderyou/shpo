<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $status;

    public function with()
    {
        // Define all status color sets
        $map = [
            'draft' => [
                'label' => 'Draft',
                'bg' => 'bg-slate-50',
                'text' => 'text-slate-600',
                'ring' => 'ring-slate-200',
            ],
            'submitted' => [
                'label' => 'Submitted',
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'ring' => 'ring-blue-200',
            ],
            'in_review' => [
                'label' => 'In Review',
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-700',
                'ring' => 'ring-amber-200',
            ],
            'approved' => [
                'label' => 'Approved',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-700',
                'ring' => 'ring-emerald-200',
            ],
            'rejected' => [
                'label' => 'Rejected',
                'bg' => 'bg-rose-50',
                'text' => 'text-rose-700',
                'ring' => 'ring-rose-200',
            ],
            'completed' => [
                'label' => 'Completed',
                'bg' => 'bg-indigo-50',
                'text' => 'text-indigo-700',
                'ring' => 'ring-indigo-200',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-500',
                'ring' => 'ring-gray-200',
            ],
        ];

        // Fallback styling
        $config = $map[$this->status] ?? [
            'label' => ucfirst(str_replace('_', ' ', $this->status)),
            'bg' => 'bg-slate-100',
            'text' => 'text-slate-500',
            'ring' => 'ring-slate-200',
        ];

        return compact('config');
    }
};
?>

<td class="px-4 py-2">
    <span class="rounded-full {{ $config['bg'] }} px-2 py-0.5 text-[11px] font-semibold {{ $config['text'] }} ring-1 ring-inset {{ $config['ring'] }}">
        {{ $config['label'] }}
    </span>
</td>
