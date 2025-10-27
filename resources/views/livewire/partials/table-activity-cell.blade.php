<?php

use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Volt\Component;

new class extends Component {
    // Props passed from parent
    public ?string $datetime = null;   // can accept Carbon|string|null
    public ?int $userId = null;

    // Optional UI props
    public string $label = 'Updated';          // e.g., "Submitted" or "Updated"
    public string $emptyText = 'No data yet';  // what to show when datetime is null

    // Expose data to the view
    public function with()
    {
        $formatted = $this->datetime
            ? ( $this->datetime instanceof Carbon
                ? $this->datetime
                : Carbon::parse($this->datetime)
              )->format('M d, Y • H:i')
            : null;

        // Lightweight lookup; you can optimize with caching/eager-load if needed
        $userName = $this->userId
            ? optional(User::find($this->userId))->name ?? '—'
            : '—';

        return compact('formatted', 'userName');
    }
};
?>

<td class="px-4 py-2 text-slate-600">
    @if($formatted)
        <div class="text-sm">{{ $formatted }}</div>
        <div class="text-xs text-slate-400">
            {{ $label }} by {{ $userName }}
        </div>
    @else
        <div class="text-sm text-slate-400 italic">{{ $emptyText }}</div>
    @endif
</td>
