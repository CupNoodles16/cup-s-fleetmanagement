@extends('layouts.app')

@section('title', 'Dashboard — TruckDispatch')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Overview')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
@endpush

@section('content')

{{-- Stat cards --}}
<div class="dashboard-stats">
    <div class="stat-card active-loads">
        <span class="stat-card-label">Active loads</span>
        <span class="stat-card-value">{{ $activeLoads }}</span>
        <span class="stat-card-sub">Currently in transit</span>
    </div>
    <div class="stat-card unassigned">
        <span class="stat-card-label">Unassigned orders</span>
        <span class="stat-card-value">{{ $unassignedOrders }}</span>
        <span class="stat-card-sub">Pending assignment</span>
    </div>
    <div class="stat-card exceptions">
        <span class="stat-card-label">Exceptions</span>
        <span class="stat-card-value">{{ $exceptions }}</span>
        <span class="stat-card-sub">Needs attention</span>
    </div>
    <div class="stat-card drivers-on">
        <span class="stat-card-label">Drivers on duty</span>
        <span class="stat-card-value">{{ $driversOnDuty }}</span>
        <span class="stat-card-sub">of {{ $totalDrivers }} total</span>
    </div>
</div>

{{-- Main grid --}}
<div class="dashboard-grid">

    {{-- Left: active loads + order queue --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Active loads</span>
                <a href="{{ route('dispatch.index') }}" class="panel-action">View all</a>
            </div>
            <div class="load-list">
                @forelse($recentLoads as $load)
                <div class="load-item">
                    <div class="load-status-dot {{ str_replace('_','-',$load->status) }}"></div>
                    <div class="load-item-info">
                        <div class="load-item-top">
                            <span class="load-item-id">{{ $load->load_number }}</span>
                            <span class="status-badge {{ str_replace('_','-',$load->status) }}">{{ ucfirst(str_replace('_',' ',$load->status)) }}</span>
                        </div>
                        <div class="load-item-route">{{ $load->order->firstStop?->city }} → {{ $load->order->lastStop?->city }}</div>
                        <div class="load-item-driver">{{ $load->driver?->user?->name ?? 'Unassigned' }} · {{ $load->vehicle?->plate_number ?? '—' }}</div>
                    </div>
                    <div class="load-item-eta">{{ $load->eta_at ? \Carbon\Carbon::parse($load->eta_at)->format('h:i A') : '—' }}</div>
                </div>
                @empty
                    <div class="panel-empty">No active loads at the moment.</div>
                @endforelse
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Unassigned orders</span>
                <a href="{{ route('orders.index') }}" class="panel-action">View all</a>
            </div>
            <div>
                @forelse($pendingOrders as $order)
                <div class="queue-item">
                    <div class="queue-item-info">
                        <div class="queue-item-id">{{ $order->order_number }} <span class="status-badge {{ $order->priority }}">{{ ucfirst($order->priority) }}</span></div>
                        <div class="queue-item-route">{{ $order->cargo_description }} · {{ number_format($order->weight_kg) }} kg</div>
                    </div>
                    <button class="queue-assign-btn"
                        onclick="window.location='{{ route('dispatch.index') }}?order={{ $order->id }}'">
                        Assign
                    </button>
                </div>
                @empty
                    <div class="panel-empty">All orders assigned.</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Right: notifications panel --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Notifications</span>
            <a href="#" class="panel-action">Mark all read</a>
        </div>
        <div class="notif-panel-list">
            @forelse(auth()->user()->notifications()->latest()->limit(15)->get() as $notif)
            <div class="notif-panel-item {{ $notif->read_at ? '' : 'unread' }}">
                <div class="notif-panel-dot {{ $notif->data['type'] ?? '' }} {{ $notif->read_at ? 'read' : '' }}"></div>
                <div class="notif-panel-body">
                    <div class="notif-panel-text">{{ $notif->data['message'] }}</div>
                    <div class="notif-panel-time">{{ $notif->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
                <div class="panel-empty">No notifications.</div>
            @endforelse
        </div>
    </div>

</div>

@endsection
