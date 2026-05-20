<?php

use Livewire\Volt\Component;
use App\Models\Load;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {

    public int  $count   = 0;
    public bool $visible = false;

    public function mount(): void
    {
        $this->refresh();
    }

    public function getExceptionsProperty(): Collection
    {
        return Load::query()
            ->with([
                'driver:id,user_id,phone',
                'driver.user:id,name',
                'order:id,order_number',
            ])
            ->select([
                'id',
                'load_number',
                'order_id',
                'driver_id',
                'status',
                'is_delayed',
                'delay_minutes',
                'eta_at',
                'updated_at',
            ])
            ->where(function ($q) {
                $q->where('status', 'failed')
                  ->orWhere(function ($q) {
                      $q->where('is_delayed', true)
                        ->where('delay_minutes', '>=', 30);
                  })
                  ->orWhere(function ($q) {
                      $q->whereNotNull('eta_at')
                        ->where('eta_at', '<', now())
                        ->whereNotIn('status', ['delivered','cancelled']);
                  });
            })
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->orderByRaw("delay_minutes DESC NULLS LAST")
            ->get();
    }

    public function refresh(): void
    {
        $previousCount = $this->count;
        $this->count   = $this->exceptions->count();

        if ($this->count > 0) {
            $this->visible = true;
            if ($this->count > $previousCount) {
                $this->dispatch('exception-detected');
            }
        } else {
            $this->visible = false;
            $this->dispatch('exceptions-cleared');
        }
    }

    public function formatIssue(string $status, bool $isDelayed, ?int $delayMinutes): string
    {
        if ($status === 'failed') {
            return 'Load failed — requires immediate attention';
        }

        if ($isDelayed && $delayMinutes >= 60) {
            return "Severely delayed — {$delayMinutes} minutes late";
        }

        if ($isDelayed && $delayMinutes) {
            return "Delayed by {$delayMinutes} minutes";
        }

        return 'Overdue — past expected delivery time';
    }

    public function formatTime(string $updatedAt): string
    {
        return \Carbon\Carbon::parse($updatedAt)->format('h:i A');
    }

};
?>

<div class="exception-pill-wrap" wire:poll.15s="refresh">

    @if($visible)

        <div x-data="{ open: false }"
            @click.outside="open = false"
            wire:ignore.self
            style="position:relative;display:inline-flex;">

            <button
                class="exception-pill visible {{ $count > 0 ? 'pulse' : '' }}"
                @click.stop="open = !open">
                <span class="exception-pill-icon">⚠</span>
                {{ $count }} {{ $count === 1 ? 'Exception' : 'Exceptions' }}
            </button>

            <div x-show="open"
                 class="exception-dropdown open"
                 @click.stop
                 style="display:none;">

                <div class="exception-dropdown-header">
                    Active Exceptions
                </div>

                @foreach($this->exceptions as $exception)
                    <div class="exception-item" wire:key="exc-{{ $exception->id }}">

                        <div class="exception-item-top">
                            <span class="exception-item-id">{{ $exception->load_number }}</span>
                            <span class="exception-item-time">{{ $this->formatTime($exception->updated_at) }}</span>
                        </div>

                        <div class="exception-item-issue">
                            {{ $this->formatIssue($exception->status, $exception->is_delayed, $exception->delay_minutes) }}
                        </div>

                        <div class="exception-item-driver">
                            {{ $exception->driver?->user?->name ?? 'Unassigned' }}
                            @if($exception->driver?->phone)
                                · {{ $exception->driver->phone }}
                            @endif
                        </div>

                        <div class="exception-item-actions">
                            <button class="exception-action-btn"
                                wire:click="$dispatch('open-assignment-modal', { loadId: {{ $exception->id }} })">
                                Reassign
                            </button>
                            <button class="exception-action-btn"
                                wire:click="$dispatch('contact-driver', { loadId: {{ $exception->id }} })">
                                Contact
                            </button>
                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    @endif

</div>
