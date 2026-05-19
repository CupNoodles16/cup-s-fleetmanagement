@extends('layouts.app')
@php $hideTopbar = true; @endphp

@section('title', 'Dispatch Board — TruckDispatch')
@section('page-title', 'Dispatch Board')
@section('breadcrumb', 'Dispatch')

@push('styles')
    <style>
        /* Remove horizontal padding from the content area for edge-to-edge layout */
        .app-content {
            padding-top: 0.5rem !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-bottom: 0 !important;
        }

        /* Reduce top padding of the dispatch header to reclaim top bar space */
        .dispatch-header {
            padding-top: 0.5rem !important;
        }

        /* Optional: if you want the stats strip and table to also touch the edges,
           reduce horizontal padding of .dispatch-stats and table cells */
        .dispatch-stats {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .load-table th,
        .load-table td {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
    </style>
    @vite('resources/css/dispatch/board.css')
@endpush

@push('scripts')
    @vite('resources/js/dispatch/board.js')
@endpush

@section('content')

<div class="dispatch-shell"
     data-tab="{{ request('tab', 'all') }}"
     data-drawer="{{ request('drawer', 'closed') }}">

    <div class="dispatch-header">

        <div class="dispatch-header-left">
            <h1 class="dispatch-title">Dispatch Board</h1>
            <livewire:dispatch.tab-bar :active-tab="request('tab', 'all')" />
        </div>

        <livewire:dispatch.exception-pill />

    </div>

    <livewire:dispatch.stats-strip />

    <div class="dispatch-body" id="dispatch-body">

        <livewire:dispatch.dispatch-board :tab="request('tab', 'all')" />

        <livewire:dispatch.queue-drawer />

    </div>

    <livewire:dispatch.load-assignment-modal />

</div>

@endsection
