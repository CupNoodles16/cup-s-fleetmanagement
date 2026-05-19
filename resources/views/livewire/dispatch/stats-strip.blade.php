<?php

use Livewire\Volt\Component;
use App\Models\Load;
use App\Models\Order;
use App\Models\Driver;

new class extends Component {

    /*
    |------------------------------------------------------------------
    | State
    |------------------------------------------------------------------
    | These properties are updated every 30 seconds via the polling
    | interval defined below. They represent global counts regardless
    | of whatever tab filter the dispatcher has active on the board.
    |------------------------------------------------------------------
    */
    public int $active          = 0;
    public int $pending         = 0;
    public int $delayed         = 0;
    public int $critical        = 0;
    public int $availableDrivers = 0;

    /*
    |------------------------------------------------------------------
    | Mount
    |------------------------------------------------------------------
    | Runs once when the component first renders.
    | Populates the stat properties immediately so the strip shows
    | real numbers before the first poll cycle fires.
    |------------------------------------------------------------------
    */
    public function mount(): void
    {
        $this->refresh();
    }

    /*
    |------------------------------------------------------------------
    | Refresh
    |------------------------------------------------------------------
    | Single method that recalculates all stats in one pass.
    | Called by mount() on first render and by the polling interval
    | every 30 seconds automatically.
    |
    | Query notes:
    | - active    = loads currently moving (not finished, not cancelled)
    | - pending   = confirmed orders with no assigned load yet
    | - delayed   = loads marked is_delayed = true
    | - critical  = loads with status failed OR exception
    | - drivers   = drivers with status available right now
    |------------------------------------------------------------------
    */
    public function refresh(): void
    {
        $this->active = Load::whereNotIn('status', [
            'delivered',
            'cancelled',
            'failed',
        ])->count();

        $this->pending = Order::where('status', 'confirmed')
            ->whereDoesntHave('loads', function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            })
            ->count();

        $this->delayed = Load::where('is_delayed', true)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        $this->critical = Load::whereIn('status', ['failed'])
            ->orWhere(function ($q) {
                $q->where('is_delayed', true)
                  ->where('delay_minutes', '>=', 30);
            })
            ->count();

        $this->availableDrivers = Driver::where('status', 'available')->count();
    }

}; ?>

{{--
    Stats strip — rendered as a flex row of labeled dot + number pairs.
    Each stat has a colored dot that matches the status dot color
    used in the load table rows below, making this strip serve
    as both a live data summary and a color legend simultaneously.

    wire:poll.30s — Livewire polls this component every 30 seconds
    and calls refresh() automatically. No manual intervention needed.
    30 seconds is a good balance between freshness and server load.
    Reduce to 15s if the client needs tighter real-time accuracy.
--}}
<div class="dispatch-stats" wire:poll.30s="refresh">

    {{-- Active --}}
    <div class="dispatch-stat">
        <span class="dispatch-stat-dot dot-active"></span>
        <span class="dispatch-stat-value">{{ $active }}</span>
        <span>Active</span>
    </div>

    <span class="dispatch-stat-divider">·</span>

    {{-- Pending assignment --}}
    <div class="dispatch-stat">
        <span class="dispatch-stat-dot dot-pending"></span>
        <span class="dispatch-stat-value">{{ $pending }}</span>
        <span>Pending</span>
    </div>

    <span class="dispatch-stat-divider">·</span>

    {{-- Delayed --}}
    <div class="dispatch-stat">
        <span class="dispatch-stat-dot dot-delayed"></span>
        <span class="dispatch-stat-value">{{ $delayed }}</span>
        <span>Delayed</span>
    </div>

    <span class="dispatch-stat-divider">·</span>

    {{-- Critical --}}
    <div class="dispatch-stat">
        <span class="dispatch-stat-dot dot-critical"></span>
        <span class="dispatch-stat-value">{{ $critical }}</span>
        <span>Critical</span>
    </div>

    <span class="dispatch-stat-divider">·</span>

    {{-- Available drivers --}}
    <div class="dispatch-stat">
        <span class="dispatch-stat-dot dot-drivers"></span>
        <span class="dispatch-stat-value">{{ $availableDrivers }}</span>
        <span>Avail. Drivers</span>
    </div>

</div>
