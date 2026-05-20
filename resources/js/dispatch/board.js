document.addEventListener('DOMContentLoaded', function () {

    /* ── References ── */
    const shell = document.querySelector('.dispatch-shell');
    const body  = document.getElementById('dispatch-body');

    /* ============================================================
       1. INITIAL STATE FROM URL
    ============================================================ */
    const initialDrawer = shell?.dataset.drawer ?? 'closed';

    if (initialDrawer === 'open' && body) {
        body.classList.add('drawer-open');
        updateDrawerToggleArrow(true);
    }

    /* ============================================================
       2. QUEUE DRAWER TOGGLE
       Listens for Livewire event from queue-drawer component.
       Removed direct click listener — was double-firing with Livewire.
    ============================================================ */
    window.addEventListener('drawer-toggled', function (e) {

        if (!body) return;
        const isOpen = e.detail.open;
        body.classList.toggle('drawer-open', isOpen);
        updateDrawerToggleArrow(isOpen);
        updateUrl({ drawer: isOpen ? 'open' : 'closed' });
    });

    function updateDrawerToggleArrow(isOpen) {
        const arrow = document.querySelector('.toggle-arrow');
        if (arrow) {
            arrow.textContent = isOpen ? '→|' : '|←';
        }
    }

    /* ============================================================
       3. TAB SWITCHING
    ============================================================ */
    window.addEventListener('dispatch-tab-changed', function (e) {
        updateUrl({ tab: e.detail.tab });
    });

    /* ============================================================
       4. EXCEPTION PILL DROPDOWN
       Removed — handled entirely by Livewire wire:click="toggleDropdown"
       and Alpine @click.outside in exception-pill.blade.php.
       JS was conflicting with Livewire DOM re-renders.
    ============================================================ */

    /* ============================================================
       5. EXCEPTION PILL ENTRANCE ANIMATION
    ============================================================ */
    window.addEventListener('exception-detected', function () {
        const pill = document.querySelector('.exception-pill');
        if (!pill) return;

        pill.classList.add('visible');
        pill.classList.remove('pulse');
        void pill.offsetWidth;
        pill.classList.add('pulse');
    });

    window.addEventListener('exceptions-cleared', function () {
        const pill = document.querySelector('.exception-pill');
        if (!pill) return;

        pill.classList.remove('visible', 'pulse');
    });

    /* ============================================================
       6. URL QUERY STRING HELPER
    ============================================================ */
    function updateUrl(params) {
        const url = new URL(window.location.href);

        Object.entries(params).forEach(([key, value]) => {
            if (value === null || value === undefined) {
                url.searchParams.delete(key);
            } else {
                url.searchParams.set(key, value);
            }
        });

        window.history.replaceState({}, '', url.toString());
    }

    /* ============================================================
       7. LIVEWIRE NAVIGATION AWARENESS
    ============================================================ */
    document.addEventListener('livewire:navigated', function () {
        const currentUrl  = new URL(window.location.href);
        const drawerState = currentUrl.searchParams.get('drawer');

        if (drawerState === 'open' && body) {
            body.classList.add('drawer-open');
            updateDrawerToggleArrow(true);
        }
    });

});
