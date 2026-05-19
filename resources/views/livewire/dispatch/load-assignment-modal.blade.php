<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\Driver;
use App\Models\Load;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

new class extends Component {

    public bool  $open      = false;
    public ?int  $orderId   = null;
    public ?int  $loadId    = null;
    public ?int  $selectedDriver = null;
    public ?int  $selectedVehicle = null;
    public string $assignmentType = 'manual';
    public string $errorMessage   = '';
    public bool  $assigning  = false;
    public bool  $success    = false;

    /*
    |------------------------------------------------------------------
    | Listen for open events from dispatch-board and queue-drawer
    |------------------------------------------------------------------
    */
    #[\Livewire\Attributes\On('open-assignment-modal')]
    public function openModal(?int $orderId = null, ?int $loadId = null): void
    {
        $this->reset([
            'selectedDriver',
            'selectedVehicle',
            'errorMessage',
            'assigning',
            'success',
        ]);

        $this->orderId = $orderId;
        $this->loadId  = $loadId;
        $this->open    = true;
    }

    public function closeModal(): void
    {
        $this->open    = false;
        $this->orderId = null;
        $this->loadId  = null;
    }

    /*
    |------------------------------------------------------------------
    | The order being assigned
    |------------------------------------------------------------------
    */
    public function getOrderProperty(): ?Order
    {
        if ($this->orderId) {
            return Order::with(['customer:id,company_name', 'stops'])
                ->find($this->orderId);
        }

        if ($this->loadId) {
            return Load::find($this->loadId)?->order()->with(['customer:id,company_name', 'stops'])->first();
        }

        return null;
    }

    /*
    |------------------------------------------------------------------
    | Eligible drivers
    | Filtered by: available/on_trip status, HOS, truck type match
    | Scored by:   proximity to pickup, HOS remaining, performance
    |------------------------------------------------------------------
    */
    public function getDriversProperty(): Collection
    {
        if (!$this->order) return collect();

        $requiredType = $this->order->required_vehicle_type;

        return Driver::query()
            ->with([
                'user:id,name',
                'phone',
                'currentVehicle:id,plate_number,type,capacity_kg',
            ])
            ->select([
                'id',
                'user_id',
                'current_vehicle_id',
                'status',
                'hos_remaining_minutes',
                'last_lat',
                'last_lng',
                'performance_rating',
                'total_deliveries',
                'phone',
            ])
            ->whereIn('status', ['available', 'on_trip'])
            ->where('hos_remaining_minutes', '>=', 120)
            ->where(function ($q) use ($requiredType) {
                if ($requiredType !== 'any') {
                    $q->whereHas('currentVehicle', function ($vq) use ($requiredType) {
                        $vq->where('type', $requiredType)
                           ->where('status', 'available');
                    });
                }
            })
            ->orderByRaw("
                (hos_remaining_minutes * 0.4) +
                (performance_rating * 20)
                DESC
            ")
            ->limit(8)
            ->get();
    }

    public function selectDriver(int $driverId): void
    {
        $this->selectedDriver  = $driverId;
        $this->selectedVehicle = Driver::find($driverId)?->current_vehicle_id;
        $this->errorMessage    = '';
    }

    public function formatHos(int $minutes): string
    {
        $hours   = intdiv($minutes, 60);
        $mins    = $minutes % 60;
        return "{$hours}h {$mins}m";
    }

    public function hosBarWidth(int $minutes): int
    {
        // Max HOS is 600 minutes (10 hours)
        return min(100, intval(($minutes / 600) * 100));
    }

    public function hosBarColor(int $minutes): string
    {
        if ($minutes >= 360) return '#4a7c59';
        if ($minutes >= 180) return '#b8892a';
        return '#8b2a2a';
    }

    /*
    |------------------------------------------------------------------
    | Confirm assignment
    |------------------------------------------------------------------
    */
    public function confirm(): void
    {
        if (!$this->selectedDriver) {
            $this->errorMessage = 'Please select a driver before confirming.';
            return;
        }

        if (!$this->order) {
            $this->errorMessage = 'Order not found.';
            return;
        }

        $this->assigning = true;

        try {
            DB::transaction(function () {

                // Create or update load
                $load = $this->loadId
                    ? Load::findOrFail($this->loadId)
                    : new Load();

                $load->order_id        = $this->order->id;
                $load->driver_id       = $this->selectedDriver;
                $load->vehicle_id      = $this->selectedVehicle;
                $load->assigned_by     = auth()->id();
                $load->status          = 'assigned';
                $load->assignment_type = 'manual';
                $load->assigned_at     = now();

                if (!$this->loadId) {
                    $load->load_number = 'LD-' . str_pad(
                        Load::max('id') + 1, 4, '0', STR_PAD_LEFT
                    );
                }

                $load->save();

                // Update order status
                $this->order->update(['status' => 'assigned']);

                // Update driver status
                Driver::where('id', $this->selectedDriver)
                      ->update(['status' => 'on_trip']);

                // Update vehicle status
                if ($this->selectedVehicle) {
                    Vehicle::where('id', $this->selectedVehicle)
                           ->update(['status' => 'on_trip']);
                }

                // Log status change
                $load->statusLogs()->create([
                    'from_status' => $this->loadId ? 'unassigned' : null,
                    'to_status'   => 'assigned',
                    'changed_by'  => auth()->id(),
                    'source'      => 'dispatcher',
                ]);

            });

            $this->success = true;

            // Notify dispatch board and queue drawer to refresh
            $this->dispatch('load-assigned');

        } catch (\Throwable $e) {
            $this->errorMessage = 'Assignment failed. Please try again.';
        } finally {
            $this->assigning = false;
        }
    }

}; ?>
<div>
    @if($open)
    <div class="modal-overlay" wire:click.self="closeModal">

        <div class="modal-container">

            {{-- Modal header --}}
            <div class="modal-header">
                <div class="modal-header-left">
                    <span class="modal-title">Assign Driver</span>
                    @if($this->order)
                        <span class="modal-subtitle">
                            {{ $this->order->order_number }}
                            · {{ $this->order->customer?->company_name }}
                        </span>
                    @endif
                </div>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>

            {{-- Order summary strip --}}
            @if($this->order)
            <div class="modal-order-strip">
                <div class="modal-order-field">
                    <span class="modal-order-label">Cargo</span>
                    <span class="modal-order-value">{{ $this->order->cargo_description }}</span>
                </div>
                <div class="modal-order-field">
                    <span class="modal-order-label">Weight</span>
                    <span class="modal-order-value">{{ number_format($this->order->weight_kg) }} kg</span>
                </div>
                <div class="modal-order-field">
                    <span class="modal-order-label">Vehicle needed</span>
                    <span class="modal-order-value">
                        {{ ucfirst(str_replace('_', ' ', $this->order->required_vehicle_type)) }}
                    </span>
                </div>
                <div class="modal-order-field">
                    <span class="modal-order-label">Deadline</span>
                    <span class="modal-order-value">
                        {{ $this->order->delivery_deadline_at
                            ? \Carbon\Carbon::parse($this->order->delivery_deadline_at)->format('M d, h:i A')
                            : '—' }}
                    </span>
                </div>
            </div>
            @endif

            {{-- Error message --}}
            @if($errorMessage)
                <div class="modal-error">{{ $errorMessage }}</div>
            @endif

            {{-- Success state --}}
            @if($success)
                <div class="modal-success">
                    <span>Load assigned successfully.</span>
                    <button class="modal-close-btn" wire:click="closeModal">
                        Close
                    </button>
                </div>
            @else

                {{-- Driver list --}}
                <div class="modal-driver-list">

                    @if($this->drivers->isEmpty())
                        <div class="modal-empty">
                            No eligible drivers available for this load.
                        </div>
                    @else
                        <div class="modal-section-label">
                            Select a driver — {{ $this->drivers->count() }} eligible
                        </div>

                        @foreach($this->drivers as $driver)
                            <div
                                class="modal-driver-card {{ $selectedDriver === $driver->id ? 'selected' : '' }}"
                                wire:click="selectDriver({{ $driver->id }})"
                                wire:key="driver-{{ $driver->id }}">

                                <div class="modal-driver-avatar">
                                    {{ strtoupper(substr($driver->user?->name ?? 'D', 0, 2)) }}
                                </div>

                                <div class="modal-driver-info">
                                    <div class="modal-driver-name">
                                        {{ $driver->user?->name ?? '—' }}
                                    </div>
                                    <div class="modal-driver-meta">
                                        {{ $driver->currentVehicle?->plate_number ?? 'No vehicle' }}
                                        · {{ ucfirst(str_replace('_',' ',$driver->currentVehicle?->type ?? '')) }}
                                    </div>
                                    <div class="modal-driver-meta">
                                        {{ number_format($driver->currentVehicle?->capacity_kg ?? 0) }} kg capacity
                                    </div>

                                    <div class="modal-hos-wrap">
                                        <div class="modal-hos-bar">
                                            <div class="modal-hos-fill"
                                                 style="
                                                    width: {{ $this->hosBarWidth($driver->hos_remaining_minutes) }}%;
                                                    background: {{ $this->hosBarColor($driver->hos_remaining_minutes) }};
                                                 ">
                                            </div>
                                        </div>
                                        <span class="modal-hos-label">
                                            HOS {{ $this->formatHos($driver->hos_remaining_minutes) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="modal-driver-right">
                                    <div class="modal-driver-rating">
                                        ★ {{ number_format($driver->performance_rating, 1) }}
                                    </div>
                                    <span class="modal-driver-status {{ $driver->status }}">
                                        {{ ucfirst(str_replace('_',' ',$driver->status)) }}
                                    </span>
                                    @if($selectedDriver === $driver->id)
                                        <span class="modal-selected-check">✓</span>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    @endif

                </div>

                {{-- Modal footer --}}
                <div class="modal-footer">
                    <button class="modal-cancel-btn" wire:click="closeModal">
                        Cancel
                    </button>
                    <button
                        class="modal-confirm-btn {{ !$selectedDriver ? 'disabled' : '' }}"
                        wire:click="confirm"
                        wire:loading.attr="disabled"
                        @disabled(!$selectedDriver || $assigning)>
                        <span wire:loading.remove wire:target="confirm">
                            Confirm Assignment
                        </span>
                        <span wire:loading wire:target="confirm">
                            Assigning...
                        </span>
                    </button>
                </div>

            @endif

        </div>

    </div>
    @endif
</div>
