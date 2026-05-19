<?php

use Livewire\Volt\Component;
use App\Models\Load;
use App\Models\Order;

new class extends Component {

    public string $activeTab = 'all';

    public int $countAll       = 0;
    public int $countEnRoute   = 0;
    public int $countPending   = 0;
    public int $countDelayed   = 0;
    public int $countDelivered = 0;

    public function mount(string $activeTab = 'all'): void
    {
        $this->activeTab = $activeTab;
        $this->refresh();
    }

    public function refresh(): void
    {
        $this->countAll = Load::whereNotIn('status', [
            'cancelled',
        ])->count();

        $this->countEnRoute = Load::whereIn('status', [
            'en_route_pickup',
            'at_pickup',
            'loaded',
            'en_route_delivery',
            'at_delivery',
            'driver_accepted',
            'assigned',
        ])->count();

        $this->countPending = Order::where('status', 'confirmed')
            ->whereDoesntHave('loads', function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            })
            ->count();

        $this->countDelayed = Load::where('is_delayed', true)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        $this->countDelivered = Load::where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;

        // Emit browser event so board.js can update the URL query string
        $this->dispatch('dispatch-tab-changed', tab: $tab);

        // Emit Livewire event so dispatch-board component re-filters its rows
        $this->dispatch('tab-switched', tab: $tab);
    }

}; ?>

<div class="dispatch-tabs" wire:poll.30s="refresh">

    <button
        wire:click="switchTab('all')"
        class="dispatch-tab {{ $activeTab === 'all' ? 'active' : '' }}">
        All
        <span class="dispatch-tab-count">{{ $countAll }}</span>
    </button>

    <button
        wire:click="switchTab('en_route')"
        class="dispatch-tab {{ $activeTab === 'en_route' ? 'active' : '' }}">
        En Route
        <span class="dispatch-tab-count">{{ $countEnRoute }}</span>
    </button>

    <button
        wire:click="switchTab('pending')"
        class="dispatch-tab {{ $activeTab === 'pending' ? 'active' : '' }}">
        Pending
        <span class="dispatch-tab-count">{{ $countPending }}</span>
    </button>

    <button
        wire:click="switchTab('delayed')"
        class="dispatch-tab {{ $activeTab === 'delayed' ? 'active' : '' }}">
        Delayed
        <span class="dispatch-tab-count">{{ $countDelayed }}</span>
    </button>

    <button
        wire:click="switchTab('delivered')"
        class="dispatch-tab {{ $activeTab === 'delivered' ? 'active' : '' }}">
        Delivered Today
        <span class="dispatch-tab-count">{{ $countDelivered }}</span>
    </button>

</div>
