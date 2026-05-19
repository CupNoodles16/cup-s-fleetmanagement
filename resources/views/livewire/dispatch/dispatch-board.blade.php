<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Load;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {

    public string $tab          = 'all';
    public ?int   $expandedLoad = null;

    public function mount(string $tab = 'all'): void
    {
        $this->tab = $tab;
    }

    #[On('tab-switched')]
    public function onTabSwitched(string $tab): void
    {
        $this->tab          = $tab;
        $this->expandedLoad = null;
    }

    public function getLoadsProperty(): Collection
    {
        $query = Load::query()
            ->with([
                'driver:id,user_id,phone,current_vehicle_id',
                'driver.user:id,name',
                'vehicle:id,plate_number,type,capacity_kg',
                'order.customer:id,company_name,phone',
                'order.stops' => fn($q) => $q->orderBy('sequence')
                    ->select('id','order_id','sequence','type','city','address_line','status'),
            ])
            ->select([
                'id','load_number','order_id','driver_id',
                'vehicle_id','status','is_delayed',
                'delay_minutes','eta_at','assigned_at',
                'delivered_at','created_at',
            ]);

        return match($this->tab) {
            'en_route'  => $query->whereIn('status', [
                                'assigned','driver_accepted',
                                'en_route_pickup','at_pickup',
                                'loaded','en_route_delivery','at_delivery',
                            ])
                            ->orderByRaw("is_delayed DESC")
                            ->orderBy('eta_at')
                            ->get(),

            'pending'   => $query->where('status', 'unassigned')
                            ->orderBy('created_at')
                            ->get(),

            'delayed'   => $query->where('is_delayed', true)
                            ->whereNotIn('status', ['delivered','cancelled'])
                            ->orderByRaw("delay_minutes DESC")
                            ->get(),

            'delivered' => $query->where('status', 'delivered')
                            ->whereDate('delivered_at', today())
                            ->orderByDesc('delivered_at')
                            ->get(),

            default     => $query->whereNotIn('status', ['cancelled'])
                            ->orderByRaw("
                                CASE status
                                    WHEN 'en_route_delivery' THEN 0
                                    WHEN 'at_delivery'       THEN 1
                                    WHEN 'loaded'            THEN 2
                                    WHEN 'en_route_pickup'   THEN 3
                                    WHEN 'at_pickup'         THEN 4
                                    WHEN 'driver_accepted'   THEN 5
                                    WHEN 'assigned'          THEN 6
                                    WHEN 'unassigned'        THEN 7
                                    ELSE 8
                                END
                            ")
                            ->orderByRaw("is_delayed DESC")
                            ->get(),
        };
    }

    public function toggleRow(int $loadId): void
    {
        $this->expandedLoad = $this->expandedLoad === $loadId
            ? null
            : $loadId;
    }

    public function getStatusDotClass(string $status, bool $isDelayed): string
    {
        if ($isDelayed) return 'status-delayed';

        return match($status) {
            'en_route_pickup',
            'en_route_delivery',
            'at_delivery'        => 'status-en-route',
            'assigned',
            'driver_accepted'    => 'status-assigned',
            'at_pickup',
            'loaded'             => 'status-at-pickup',
            'unassigned'         => 'status-pending',
            'delivered'          => 'status-delivered',
            'failed'             => 'status-exception',
            default              => 'status-assigned',
        };
    }

    public function formatEta(?string $eta, bool $isDelayed, ?int $delayMinutes): string
    {
        if (!$eta) return '—';
        if ($isDelayed && $delayMinutes) return "+{$delayMinutes}m late";
        return \Carbon\Carbon::parse($eta)->format('h:i A');
    }

};
?>

<div class="load-table-wrap" wire:poll.15s>

    @if($this->loads->isEmpty())
        <div class="load-table-empty">
            No loads found for this filter.
        </div>
    @else
        <table class="load-table">
            <thead>
                <tr>
                    <th>Load ID</th>
                    <th>Driver</th>
                    <th>Route</th>
                    <th>Status</th>
                    <th>ETA</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->loads as $load)

                    {{-- Main row --}}
                    <tr wire:click="toggleRow({{ $load->id }})"
                        wire:key="load-{{ $load->id }}"
                        class="{{ $this->expandedLoad === $load->id ? 'expanded' : '' }}">

                        <td>{{ $load->load_number }}</td>

                        <td>
                            {{ $load->driver?->user?->name ?? '—' }}
                            @if($load->vehicle)
                                <span style="color:var(--color-text-muted);font-size:0.72rem;">
                                    · {{ $load->vehicle->plate_number }}
                                </span>
                            @endif
                        </td>

                        <td>
                            @php
                                $pickup   = $load->order?->stops->firstWhere('type','pickup');
                                $delivery = $load->order?->stops->where('type','delivery')->last();
                            @endphp
                            {{ $pickup?->city ?? '—' }} → {{ $delivery?->city ?? '—' }}
                        </td>

                        <td>
                            <span class="load-status-dot {{ $this->getStatusDotClass($load->status, $load->is_delayed) }}"></span>
                        </td>

                        <td class="{{ $load->is_delayed ? ($load->delay_minutes >= 30 ? 'load-eta-critical' : 'load-eta-delayed') : '' }}">
                            {{ $this->formatEta($load->eta_at, $load->is_delayed, $load->delay_minutes) }}
                        </td>

                        <td style="color:var(--color-text-muted);font-size:0.75rem;text-align:right;">
                            {{ $this->expandedLoad === $load->id ? '▲' : '▼' }}
                        </td>

                    </tr>

                    {{-- Expanded detail row --}}
                    @if($this->expandedLoad === $load->id)
                        <tr class="load-row-expanded"
                            wire:key="load-expanded-{{ $load->id }}">
                            <td colspan="6">

                                <div class="load-expanded-inner">
                                    <div class="load-expanded-group">
                                        <span class="load-expanded-label">Vehicle</span>
                                        <span class="load-expanded-value">
                                            {{ $load->vehicle?->plate_number ?? '—' }}
                                            · {{ ucfirst(str_replace('_',' ', $load->vehicle?->type ?? '')) }}
                                        </span>
                                        <span class="load-expanded-value" style="color:var(--color-text-muted);">
                                            {{ number_format($load->vehicle?->capacity_kg ?? 0) }} kg capacity
                                        </span>
                                    </div>

                                    <div class="load-expanded-group">
                                        <span class="load-expanded-label">Cargo</span>
                                        <span class="load-expanded-value">
                                            {{ $load->order?->cargo_description ?? '—' }}
                                        </span>
                                        <span class="load-expanded-value" style="color:var(--color-text-muted);">
                                            {{ number_format($load->order?->weight_kg ?? 0) }} kg
                                            · {{ ucfirst($load->order?->cargo_type ?? '') }}
                                        </span>
                                    </div>

                                    <div class="load-expanded-group">
                                        <span class="load-expanded-label">Customer</span>
                                        <span class="load-expanded-value">
                                            {{ $load->order?->customer?->company_name ?? '—' }}
                                        </span>
                                        <span class="load-expanded-value" style="color:var(--color-text-muted);">
                                            {{ $load->order?->customer?->phone ?? '—' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Stops --}}
                                @if($load->order?->stops->count())
                                    <div style="margin-top:0.5rem;padding-top:0.5rem;border-top:1px solid var(--color-beige-dark);">
                                        <span class="load-expanded-label">Stops</span>
                                        <div style="margin-top:0.4rem;display:flex;flex-direction:column;gap:3px;">
                                            @foreach($load->order->stops->sortBy('sequence') as $stop)
                                                <div style="font-size:0.78rem;color:var(--color-text-secondary);display:flex;align-items:center;gap:6px;">
                                                    <span style="color:var(--color-text-muted);font-size:0.68rem;min-width:12px;">
                                                        {{ $loop->iteration }}.
                                                    </span>
                                                    <span style="
                                                        width:6px;height:6px;border-radius:50%;flex-shrink:0;
                                                        background:{{ $stop->status === 'completed' ? '#4a7c59' : ($stop->status === 'en_route' ? '#c97b2a' : '#d0cec8') }};
                                                    "></span>
                                                    {{ $stop->address_line }}, {{ $stop->city }}
                                                    <span style="color:var(--color-text-muted);font-size:0.7rem;margin-left:auto;">
                                                        {{ ucfirst($stop->type) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Quick actions --}}
                                <div class="load-expanded-actions">
                                    <button class="load-action-btn"
                                        wire:click.stop="$dispatch('open-assignment-modal', { loadId: {{ $load->id }} })">
                                        Reassign
                                    </button>
                                    <button class="load-action-btn danger"
                                        wire:click.stop="$dispatch('report-issue', { loadId: {{ $load->id }} })">
                                        Report Issue
                                    </button>
                                    @if($load->driver?->phone)
                                        <a class="load-action-btn"
                                            href="tel:{{ $load->driver->phone }}"
                                            wire:click.stop>
                                            Contact Driver
                                        </a>
                                    @endif
                                </div>

                            </td>
                        </tr>
                    @endif

                @endforeach
            </tbody>
        </table>
    @endif

</div>
