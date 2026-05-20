<aside class="sidebar">

    <div class="sidebar-brand">
        <span class="sidebar-brand-name">TruckDispatch</span>
        <button class="sidebar-toggle" id="sidebar-toggle" title="Collapse sidebar">
            <span class="toggle-arrow">←</span>
        </button>
    </div>

    <div class="sidebar-collapsed-expand" id="sidebar-collapsed-expand" style="display: none;">
        <button class="sidebar-expand-vertical" title="Expand sidebar">
            <span class="expand-arrow">→</span>
            <span class="expand-text">Click to Expand Sidebar</span>
        </button>
    </div>

    <nav class="sidebar-nav">

        {{-- Overview --}}
        <div class="sidebar-section">
            <span class="sidebar-section-label">Overview</span>
            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        </div>

        {{-- Dispatch --}}
        <div class="sidebar-section">
            <span class="sidebar-section-label">Dispatch</span>
            <a href="{{ route('dispatch.index') }}"
            class="sidebar-link {{ request()->routeIs('dispatch.*') ? 'active' : '' }}">
                Dispatch Board
                @if($unassignedCount > 0)
                    <span class="sidebar-badge {{ $unassignedCount > 5 ? 'urgent' : '' }}">
                        {{ $unassignedCount }}
                    </span>
                @else
                    <span class="sidebar-wip">🚧 Soon</span>
                @endif
            </a>
            <a href="#" class="sidebar-link">
                Active Loads
                <span class="sidebar-wip">🚧 Soon</span>
            </a>
            <a href="#" class="sidebar-link">
                Load Assignment
                <span class="sidebar-wip">🚧 Soon</span>
            </a>
        </div>

        {{-- Orders --}}
        <div class="sidebar-section">
            <span class="sidebar-section-label">Orders</span>
            <a href="#" class="sidebar-link">
                All Orders
                <span class="sidebar-wip">🚧 Soon</span>
            </a>
            <a href="#" class="sidebar-link">
                Create Order
                <span class="sidebar-wip">🚧 Soon</span>
            </a>
            <a href="#" class="sidebar-link">
                Order Tracking
                <span class="sidebar-wip">🚧 Soon</span>
            </a>
        </div>

        {{-- Fleet --}}
        <div class="sidebar-section">
            <span class="sidebar-section-label">Fleet</span>

            {{-- Drivers dropdown --}}
            <div class="sidebar-dropdown"
                 x-data="{ open: {{ request()->routeIs('fleet.drivers.*') ? 'true' : 'false' }} }">
                <button class="sidebar-link sidebar-link-parent"
                        @click="open = !open"
                        :class="{ 'active': open }">
                    Drivers
                    <span class="sidebar-wip">🚧 Soon</span>
                    <span class="sidebar-arrow" :class="{ 'rotated': open }">›</span>
                </button>
                <div class="sidebar-submenu" x-show="open"
                     x-transition:enter="submenu-enter"
                     x-transition:enter-start="submenu-enter-start"
                     x-transition:enter-end="submenu-enter-end"
                     x-transition:leave="submenu-leave"
                     x-transition:leave-start="submenu-leave-start"
                     x-transition:leave-end="submenu-leave-end">
                    <div class="sidebar-submenu-inner">
                        <a href="#" class="sidebar-sublink">All Drivers</a>
                        <a href="#" class="sidebar-sublink">Add New Driver</a>
                        <a href="#" class="sidebar-sublink">Driver Documents</a>
                    </div>
                </div>
            </div>

            {{-- Vehicles dropdown --}}
            <div class="sidebar-dropdown"
                 x-data="{ open: {{ request()->routeIs('fleet.vehicles.*') ? 'true' : 'false' }} }">
                <button class="sidebar-link sidebar-link-parent"
                        @click="open = !open"
                        :class="{ 'active': open }">
                    Vehicles
                    <span class="sidebar-wip">🚧 Soon</span>
                    <span class="sidebar-arrow" :class="{ 'rotated': open }">›</span>
                </button>
                <div class="sidebar-submenu" x-show="open"
                     x-transition:enter="submenu-enter"
                     x-transition:enter-start="submenu-enter-start"
                     x-transition:enter-end="submenu-enter-end"
                     x-transition:leave="submenu-leave"
                     x-transition:leave-start="submenu-leave-start"
                     x-transition:leave-end="submenu-leave-end">
                    <div class="sidebar-submenu-inner">
                        <a href="#" class="sidebar-sublink">All Vehicles</a>
                        <a href="#" class="sidebar-sublink">Add New Vehicle</a>
                        <a href="#" class="sidebar-sublink">Maintenance Schedule</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Finance --}}
        <div class="sidebar-section">
            <span class="sidebar-section-label">Finance</span>

            {{-- Invoices dropdown --}}
            <div class="sidebar-dropdown"
                 x-data="{ open: {{ request()->routeIs('finance.invoices.*') ? 'true' : 'false' }} }">
                <button class="sidebar-link sidebar-link-parent"
                        @click="open = !open"
                        :class="{ 'active': open }">
                    Invoices
                    <span class="sidebar-wip">🚧 Soon</span>
                    <span class="sidebar-arrow" :class="{ 'rotated': open }">›</span>
                </button>
                <div class="sidebar-submenu" x-show="open"
                     x-transition:enter="submenu-enter"
                     x-transition:enter-start="submenu-enter-start"
                     x-transition:enter-end="submenu-enter-end"
                     x-transition:leave="submenu-leave"
                     x-transition:leave-start="submenu-leave-start"
                     x-transition:leave-end="submenu-leave-end">
                    <div class="sidebar-submenu-inner">
                        <a href="#" class="sidebar-sublink">All Invoices</a>
                        <a href="#" class="sidebar-sublink">Generate Invoice</a>
                    </div>
                </div>
            </div>

            {{-- Reports dropdown --}}
            <div class="sidebar-dropdown"
                 x-data="{ open: {{ request()->routeIs('finance.reports.*') ? 'true' : 'false' }} }">
                <button class="sidebar-link sidebar-link-parent"
                        @click="open = !open"
                        :class="{ 'active': open }">
                    Reports
                    <span class="sidebar-wip">🚧 Soon</span>
                    <span class="sidebar-arrow" :class="{ 'rotated': open }">›</span>
                </button>
                <div class="sidebar-submenu" x-show="open"
                     x-transition:enter="submenu-enter"
                     x-transition:enter-start="submenu-enter-start"
                     x-transition:enter-end="submenu-enter-end"
                     x-transition:leave="submenu-leave"
                     x-transition:leave-start="submenu-leave-start"
                     x-transition:leave-end="submenu-leave-end">
                    <div class="sidebar-submenu-inner">
                        <a href="#" class="sidebar-sublink">Delivery Reports</a>
                        <a href="#" class="sidebar-sublink">Driver Performance</a>
                        <a href="#" class="sidebar-sublink">Revenue Summary</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Management — superadmin only --}}
        @if(auth()->user()->hasRole('superadmin'))
        <div class="sidebar-section">
            <span class="sidebar-section-label">Management</span>

            {{-- Users dropdown --}}
            <div class="sidebar-dropdown"
                 x-data="{ open: {{ request()->routeIs('management.users.*') || request()->routeIs('register') ? 'true' : 'false' }} }">
                <button class="sidebar-link sidebar-link-parent"
                        @click="open = !open"
                        :class="{ 'active': open }">
                    Users
                    <span class="sidebar-arrow" :class="{ 'rotated': open }">›</span>
                </button>
                <div class="sidebar-submenu" x-show="open"
                     x-transition:enter="submenu-enter"
                     x-transition:enter-start="submenu-enter-start"
                     x-transition:enter-end="submenu-enter-end"
                     x-transition:leave="submenu-leave"
                     x-transition:leave-start="submenu-leave-start"
                     x-transition:leave-end="submenu-leave-end">
                    <div class="sidebar-submenu-inner">
                        <a href="#" class="sidebar-sublink">
                            All Users
                            <span class="sidebar-wip">🚧 Soon</span>
                        </a>
                        <a href="{{ route('register') }}"
                           class="sidebar-sublink {{ request()->routeIs('register') ? 'active' : '' }}">
                            Add New User
                        </a>
                    </div>
                </div>
            </div>

            <a href="#" class="sidebar-link">
                Roles & Permissions
                <span class="sidebar-wip">🚧 Soon</span>
            </a>
            <a href="#" class="sidebar-link">
                System Logs
                <span class="sidebar-wip">🚧 Soon</span>
            </a>
            <a href="#" class="sidebar-link">
                Settings
                <span class="sidebar-wip">🚧 Soon</span>
            </a>
        </div>
        @endif

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-user-role">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
        <a href="{{ route('logout') }}" class="sidebar-logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                 stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>

</aside>
