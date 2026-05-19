<?php

use Livewire\Volt\Component;
use App\Models\Load;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {

    public int  $count       = 0;
    public bool $visible     = false;
    public bool $dropdownOpen = false;

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

            // If count increased dispatch browser event to trigger
            // slide-in + pulse animation via board.js
            if ($this->count > $previousCount) {
                $this->dispatch('exception-detected');
            }
        } else {
            $this->visible = false;
            $this->dispatch('exceptions-cleared');
        }
    }

    public function toggleDropdown(): void
    {
        $this->dropdownOpen = !$this->dropdownOpen;
    }

    public function formatIssue(string $status, bool $isDelayed, ?int $delayMinutes): string
    {
        if ($status === 'failed') {
            return 'Load failed — requires attention';
        }

        if ($isDelayed && $delayMinutes) {
            return "Delayed by {$delayMinutes} minutes";
        }

        return 'Exception reported';
    }

    public function formatTime(string $updatedAt): string
    {
        return \Carbon\Carbon::parse($updatedAt)->format('h:i A');
    }

}; ?>

{{--
    Exception pill — only renders when count > 0.
    Visibility and animation are controlled by:
    - CSS classes .visible and .pulse on the pill element
    - board.js listens for exception-detected and exceptions-cleared
      browser events and toggles those classes
    The pill itself is always in the DOM when count > 0 so Livewire
    can update the count without re-triggering the slide animation
    on every poll cycle. The animation only fires when count increases.
--}}

<div class="exception-pill-wrap" wire:poll.15s="refresh">

    @if($visible)
        <button
            class="exception-pill visible {{ $count > 0 ? 'pulse' : '' }}"
            wire:click="toggleDropdown"
            x-data
            @click.outside="$wire.dropdownOpen = false">

            <span class="exception-pill-icon">⚠</span>
            {{ $count }} {{ $count === 1 ? 'Exception' : 'Exceptions' }}

            {{-- Dropdown --}}
            @if($dropdownOpen)
                <div class="exception-dropdown open"
                     @click.stop>

                    <div class="exception-dropdown-header">
                        Active Exceptions
                    </div>

                    @foreach($this->exceptions as $exception)
                        <div class="exception-item"
                             wire:key="exc-{{ $exception->id }}">

                            <div class="exception-item-top">
                                <span class="exception-item-id">
                                    {{ $exception->load_number }}
                                </span>
                                <span class="exception-item-time">
                                    {{ $this->formatTime($exception->updated_at) }}
                                </span>
                            </div>

                            <div class="exception-item-issue">
                                {{ $this->formatIssue(
                                    $exception->status,
                                    $exception->is_delayed,
                                    $exception->delay_minutes
                                ) }}
                            </div>

                            <div class="exception-item-driver">
                                {{ $exception->driver?->user?->name ?? 'Unassigned' }}
                                @if($exception->driver?->phone)
                                    · {{ $exception->driver->phone }}
                                @endif
                            </div>

                            <div class="exception-item-actions">
                                <button class="exception-action-btn"
                                    wire:click="$dispatch('open-assignment-modal',
                                        { loadId: {{ $exception->id }} })">
                                    Reassign
                                </button>
                                <button class="exception-action-btn"
                                    wire:click="$dispatch('contact-driver',
                                        { loadId: {{ $exception->id }} })">
                                    Contact
                                </button>
                            </div>

                        </div>
                    @endforeach

                </div>
            @endif

        </button>
    @endif

</div>
