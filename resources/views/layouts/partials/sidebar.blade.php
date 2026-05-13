<aside class="sidebar">

    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg viewBox="0 0 24 24"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <span class="sidebar-brand-name">TruckDispatch</span>
    </div>

    <nav class="sidebar-nav">

        <div class="sidebar-section">
            <span class="sidebar-section-label">Overview</span>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </div>

        <div class="sidebar-section">
            <span class="sidebar-section-label">Dispatch</span>
            <a href="#" class="sidebar-link {{ request()->routeIs('dispatch.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Dispatch Board
                @if($unassignedCount > 0)
                    <span class="sidebar-badge {{ $unassignedCount > 5 ? 'urgent' : '' }}">{{ $unassignedCount }}</span>
                @endif
            </a>
            <a href="#" class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Orders
            </a>
        </div>

        <div class="sidebar-section">
            <span class="sidebar-section-label">Fleet</span>
            <a href="#" class="sidebar-link {{ request()->routeIs('fleet.drivers.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Drivers
            </a>
            <a href="#" class="sidebar-link {{ request()->routeIs('fleet.vehicles.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                Vehicles
            </a>
        </div>

        <div class="sidebar-section">
            <span class="sidebar-section-label">Management</span>
            <a href="#" class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Customers
            </a>
            <a href="#" class="sidebar-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Finance
            </a>
        </div>

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-user-role">{{ auth()->user()->role }}</div>
        </div>
        <a href="{{ route('logout') }}" class="sidebar-logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>

</aside>
