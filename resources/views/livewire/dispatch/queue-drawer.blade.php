<?php

use Livewire\Volt\Component;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {

    public bool $isOpen = false;

    public function mount(): void
    {
        // Restore drawer state from URL if dispatcher refreshes
        $this->isOpen = request('drawer') === 'open';
    }

    public function getOrdersProperty(): Collection
    {
        return Order::query()
            ->with(['customer:id,company_name'])
            ->select([
                'id',
                'order_number',
                'customer_id',
                'cargo_description',
                'weight_kg',
                'priority',
                'delivery_deadline_at',
                'required_vehicle_type',
                'created_at',
            ])
            ->where('status', 'confirmed')
            ->whereDoesntHave('loads', function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            })
            ->orderByRaw("
                CASE priority
                    WHEN 'critical' THEN 0
                    WHEN 'urgent'   THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('delivery_deadline_at')
            ->get();
    }

    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;

        // Emit browser event so board.js updates URL and grid columns
        $this->dispatch('drawer-toggled', open: $this->isOpen);
    }

    public function assignOrder(int $orderId): void
    {
        // Emit to load-assignment-modal to open with this order pre-selected
        $this->dispatch('open-assignment-modal', orderId: $orderId);
    }

    public function formatDeadline(?string $deadline): string
    {
        if (!$deadline) return '—';

        $dt = \Carbon\Carbon::parse($deadline);

        if ($dt->isToday()) {
            return 'Today ' . $dt->format('h:i A');
        }

        if ($dt->isTomorrow()) {
            return 'Tomorrow ' . $dt->format('h:i A');
        }

        return $dt->format('M d, h:i A');
    }

    public function isOverdue(?string $deadline): bool
    {
        if (!$deadline) return false;
        return \Carbon\Carbon::parse($deadline)->isPast();
    }

}; ?>

<div class="queue-drawer">

    {{-- Toggle arrow — always visible regardless of open/closed state --}}
    <button class="queue-drawer-toggle"
            wire:click="toggle"
            title="{{ $isOpen ? 'Collapse queue' : 'Expand queue' }}">
        <span class="toggle-arrow">{{ $isOpen ? '→|' : '|←' }}</span>
        @if(!$isOpen)
            <span style="font-size:0.6rem;letter-spacing:0.12em;color:var(--color-text-muted);">
                QUEUE
            </span>
            @if($this->orders->count() > 0)
                <span style="
                    margin-top:4px;
                    font-size:0.65rem;
                    font-weight:600;
                    background:#c97b2a;
                    color:#fff;
                    border-radius:10px;
                    padding:1px 5px;
                    writing-mode:horizontal-tb;
                ">
                    {{ $this->orders->count() }}
                </span>
            @endif
        @endif
    </button>

    {{-- Drawer content — only visible when open --}}
    <div class="queue-drawer-content" wire:poll.20s>

        <div class="queue-drawer-header">
            Unassigned
            @if($this->orders->count() > 0)
                <span style="
                    margin-left:6px;
                    font-size:0.65rem;
                    font-weight:600;
                    background:#c97b2a;
                    color:#fff;
                    border-radius:10px;
                    padding:1px 6px;
                ">
                    {{ $this->orders->count() }}
                </span>
            @endif
        </div>

        <div class="queue-drawer-list">

            @forelse($this->orders as $order)
                <div class="queue-order-card" wire:key="order-{{ $order->id }}">

                    <div class="queue-order-top">
                        <span class="queue-order-id">{{ $order->order_number }}</span>
                        <span class="queue-priority-badge priority-{{ $order->priority }}">
                            {{ ucfirst($order->priority) }}
                        </span>
                    </div>

                    <div class="queue-order-route">
                        {{ $order->cargo_description }}
                    </div>

                    <div class="queue-order-meta">
                        {{ number_format($order->weight_kg) }} kg
                        · {{ ucfirst(str_replace('_', ' ', $order->required_vehicle_type)) }}
                    </div>

                    <div class="queue-order-meta" style="
                        color: {{ $this->isOverdue($order->delivery_deadline_at) ? '#8b2a2a' : 'var(--color-text-muted)' }};
                        font-weight: {{ $this->isOverdue($order->delivery_deadline_at) ? '500' : '400' }};
                    ">
                        Due: {{ $this->formatDeadline($order->delivery_deadline_at) }}
                    </div>

                    <div class="queue-order-meta" style="color:var(--color-text-muted);">
                        {{ $order->customer?->company_name ?? '—' }}
                    </div>

                    <button class="queue-assign-btn"
                            wire:click="assignOrder({{ $order->id }})">
                        Assign Driver
                    </button>

                </div>
            @empty
                <div class="panel-empty">
                    All orders assigned.
                </div>
            @endforelse

        </div>

    </div>

</div>
