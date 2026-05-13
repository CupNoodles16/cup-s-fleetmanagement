<header class="topbar">

    <div class="topbar-left">
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        <span class="topbar-breadcrumb">@yield('breadcrumb', 'Overview')</span>
    </div>

    <div class="topbar-right">

        {{-- Notifications --}}
        <div style="position:relative;">
            <button class="topbar-btn" id="notif-toggle" aria-label="Notifications">
                <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                @if($unreadNotifCount > 0)
                    <span class="notif-dot"></span>
                @endif
            </button>

            <div class="notif-dropdown" id="notif-dropdown">
                <div class="notif-dropdown-header">
                    <span class="notif-dropdown-title">Notifications</span>
                    <a href="#" class="notif-mark-all">Mark all read</a>
                </div>
                @forelse($recentNotifications as $notif)
                    <div class="notif-item {{ $notif->read_at ? '' : 'unread' }}">
                        <div class="notif-item-icon {{ $notif->data['type'] === 'exception' ? 'urgent' : '' }}">
                            <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>
                        </div>
                        <div class="notif-item-body">
                            <div class="notif-item-text">{{ $notif->data['message'] }}</div>
                            <div class="notif-item-time">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div style="padding:1rem;text-align:center;font-size:0.8rem;color:var(--color-text-muted);">No notifications</div>
                @endforelse
                <div class="notif-dropdown-footer">
                    <a href="#" class="notif-view-all">View all notifications</a>
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <a href="#" class="topbar-btn" aria-label="Settings">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
        </a>

    </div>
</header>

<script>
    document.getElementById('notif-toggle').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('notif-dropdown').classList.toggle('open');
    });
    document.addEventListener('click', function() {
        document.getElementById('notif-dropdown').classList.remove('open');
    });
</script>
