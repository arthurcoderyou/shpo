<?php

use function Livewire\Volt\{state, mount};

state([
    // 'status' shows ongoing/completed/pending + legend
    // 'info' shows outline-only numbers, no legend/status text
    'mode' => 'info',          // 'info' | 'status'
    'currentStage' => 1,       // 1=Initial, 2=Document, 3=Final (used only in status mode)
]);

mount(function (?string $mode = 'info', ?int $currentStage = 1) {
    // Allow passing via route params or <livewire:review-flow :mode="" :current-stage="">
    $this->mode = in_array($mode, ['info','status']) ? $mode : 'info';
    $this->currentStage = in_array($currentStage, [1,2,3]) ? $currentStage : 1;
});

// Helpers for classes/text
$stepClass = fn (int $step) =>
    $this->mode !== 'status'
        ? 'bg-white ring-indigo-600 text-indigo-600'
        : ($this->currentStage > $step
            ? 'bg-indigo-600 ring-indigo-600 text-white'            // done
            : ($this->currentStage === $step
                ? 'bg-white ring-indigo-600 text-indigo-600'        // in progress
                : 'bg-white ring-slate-300 text-slate-400'));        // pending

$labelClass = fn (int $step) =>
    $this->mode !== 'status'
        ? 'text-slate-900'
        : ($this->currentStage >= $step ? 'text-slate-900' : 'text-slate-400');

$subLabel = fn (int $step) =>
    $this->currentStage === $step ? 'In progress' : ($this->currentStage > $step ? 'Completed' : 'Pending');

// Connector progress right offset for STATUS mode
$progressRight = [
    1 => '87.5%',
    2 => '50%',
    3 => '12.5%',
][$this->currentStage] ?? '87.5%';

?>

<div class="w-full max-w-5xl mx-auto">
    <div class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-slate-800">Program Flow</h2>
        <p class="text-sm text-slate-500">Initial Review → Document Review → Final Review</p>

        <!-- Horizontal stepper (md+) -->
        <ol class="relative hidden md:flex items-center justify-between gap-4 mt-6">
        <!-- Track -->
        <div class="absolute left-[12.5%] right-[12.5%] top-6 h-0.5 bg-slate-200 -z-10"></div>

        <!-- Progress (STATUS mode only) -->
        @if($this->mode === 'status')
            <div class="absolute left-[12.5%] top-6 h-0.5 -z-10 bg-indigo-600 transition-all duration-300"
                style="right: {{ $progressRight }}"></div>
        @endif

        <!-- Step 1: Initial -->
        <li class="flex flex-col items-center text-center w-1/3">
            <div class="flex items-center justify-center w-12 h-12 rounded-full ring-2 {{ $stepClass(1) }}">
            @if($this->mode === 'status' && $this->currentStage > 1)
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            @else
                <span class="font-semibold">1</span>
            @endif
            </div>
            <div class="mt-3">
            <div class="text-sm font-medium {{ $labelClass(1) }}">Initial Review</div>
            @if($this->mode === 'status')
                <p class="text-xs {{ $this->currentStage === 1 ? 'text-indigo-600' : 'text-slate-500' }}">
                {{ $subLabel(1) }}
                </p>
            @endif
            </div>
        </li>

        <!-- Step 2: Document -->
        <li class="flex flex-col items-center text-center w-1/3">
            <div class="flex items-center justify-center w-12 h-12 rounded-full ring-2 {{ $stepClass(2) }}">
            @if($this->mode === 'status' && $this->currentStage > 2)
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            @else
                <span class="font-semibold">2</span>
            @endif
            </div>
            <div class="mt-3">
            <div class="text-sm font-medium {{ $labelClass(2) }}">Document Review</div>
            @if($this->mode === 'status')
                <p class="text-xs {{ $this->currentStage === 2 ? 'text-indigo-600' : 'text-slate-500' }}">
                {{ $subLabel(2) }}
                </p>
            @endif
            </div>
        </li>

        <!-- Step 3: Final -->
        <li class="flex flex-col items-center text-center w-1/3">
            <div class="flex items-center justify-center w-12 h-12 rounded-full ring-2 {{ $stepClass(3) }}">
            @if($this->mode === 'status' && $this->currentStage > 3)
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            @else
                <span class="font-semibold">3</span>
            @endif
            </div>
            <div class="mt-3">
            <div class="text-sm font-medium {{ $labelClass(3) }}">Final Review</div>
            @if($this->mode === 'status')
                <p class="text-xs {{ $this->currentStage === 3 ? 'text-indigo-600' : 'text-slate-500' }}">
                {{ $subLabel(3) }}
                </p>
            @endif
            </div>
        </li>
        </ol>

        <!-- Mobile vertical timeline (sm and down) -->
        <ol class="md:hidden mt-6 relative border-slate-200">
        @foreach ([1 => 'Initial Review', 2 => 'Document Review', 3 => 'Final Review'] as $i => $title)
            <li class="relative pl-10 {{ $i < 3 ? 'pb-6' : '' }}">
            @if($i < 3)
                <span class="absolute left-4 top-2 w-0.5 h-full bg-slate-200"></span>
            @endif
            <span class="absolute left-2 top-1 w-6 h-6 rounded-full ring-2 {{ $stepClass($i) }} flex items-center justify-center">
                @if($this->mode === 'status' && $this->currentStage > $i)
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                @else
                <span class="text-xs font-semibold">{{ $i }}</span>
                @endif
            </span>
            <div>
                <div class="text-sm font-medium {{ $labelClass($i) }}">{{ $title }}</div>
                @if($this->mode === 'status')
                <p class="text-xs {{ $this->currentStage === $i ? 'text-indigo-600' : 'text-slate-500' }}">
                    {{ $subLabel($i) }}
                </p>
                @endif
            </div>
            </li>
        @endforeach
        </ol>

        <!-- Legend (STATUS mode only) -->
        @if($this->mode === 'status')
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-full bg-indigo-600 inline-block"></span> Done
            </span>
            <span class="inline-flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-full ring-2 ring-indigo-600 inline-block bg-white"></span> In progress
            </span>
            <span class="inline-flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-full ring-2 ring-slate-300 inline-block bg-white"></span> Pending
            </span>
        </div>
        @endif
    </div>
</div>
