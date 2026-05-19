/* ============================================================
   dispatch/board.js
   Handles all non-Livewire interactivity on the dispatch board.
   Livewire/Alpine handle reactivity inside components.
   This file handles the shell-level behavior.
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ── References ── */
    const shell       = document.querySelector('.dispatch-shell');
    const body        = document.getElementById('dispatch-body');

    /* ============================================================
       1. INITIAL STATE FROM URL
       Read data attributes set by Blade from the URL query string.
       Restore the drawer and tab state on page load.
    ============================================================ */
    const initialTab    = shell?.dataset.tab    ?? 'all';
    const initialDrawer = shell?.dataset.drawer ?? 'closed';

    if (initialDrawer === 'open' && body) {
        body.classList.add('drawer-open');
        updateDrawerToggleArrow(true);
    }

    /* ============================================================
       2. QUEUE DRAWER TOGGLE
       The toggle button lives inside the queue-drawer Volt component.
       We listen for clicks on it here at the shell level so the
       grid column transition on .dispatch-body is owned by this file.
    ============================================================ */
    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('.queue-drawer-toggle');
        if (!toggleBtn || !body) return;

        const isOpen = body.classList.toggle('drawer-open');
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
       Tab clicks are handled by the Volt tab-bar component internally
       via Livewire. However we also listen for the custom browser
       event it emits so we can update the URL query string here.

       The Volt component emits:
       this.$dispatch('dispatch-tab-changed', { tab: 'en_route' })

       Livewire v4 / Volt dispatches browser events via
       $dispatch which bubbles up to window.
    ============================================================ */
    window.addEventListener('dispatch-tab-changed', function (e) {
        updateUrl({ tab: e.detail.tab });
    });

    /* ============================================================
       4. EXCEPTION PILL DROPDOWN
       Toggle the dropdown open/close when the pill is clicked.
       Close when clicking anywhere outside the pill.
    ============================================================ */
    document.addEventListener('click', function (e) {
        const pill     = e.target.closest('.exception-pill');
        const dropdown = document.querySelector('.exception-dropdown');

        if (!dropdown) return;

        if (pill) {
            dropdown.classList.toggle('open');
            return;
        }

        // Click outside — close dropdown
        if (!e.target.closest('.exception-dropdown')) {
            dropdown.classList.remove('open');
        }
    });

    /* ============================================================
       5. EXCEPTION PILL ENTRANCE ANIMATION
       Livewire emits a browser event when a new exception is detected.
       We listen for it here and trigger the slide-in + pulse animation.

       The Volt exception-pill component emits:
       this.$dispatch('exception-detected')

       When all exceptions are resolved it emits:
       this.$dispatch('exceptions-cleared')
    ============================================================ */
    window.addEventListener('exception-detected', function () {
        const pill = document.querySelector('.exception-pill');
        if (!pill) return;

        // Make visible — triggers CSS transition slide in from right
        pill.classList.add('visible');

        // Pulse animation fires after slide-in completes (0.4s delay in CSS)
        pill.classList.remove('pulse');
        void pill.offsetWidth; // force reflow to restart animation
        pill.classList.add('pulse');
    });

    window.addEventListener('exceptions-cleared', function () {
        const pill = document.querySelector('.exception-pill');
        if (!pill) return;

        pill.classList.remove('visible', 'pulse');
    });

    /* ============================================================
       6. URL QUERY STRING HELPER
       Updates the browser URL without triggering a page reload.
       Preserves existing query params and only updates the ones passed.
       This keeps the tab and drawer state bookmarkable and shareable.
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
       When Livewire updates the DOM after a poll cycle, some elements
       may be re-rendered. Re-apply the drawer open state after
       any Livewire DOM update so the grid does not reset.
    ============================================================ */
    document.addEventListener('livewire:navigated', function () {
        const currentUrl = new URL(window.location.href);
        const drawerState = currentUrl.searchParams.get('drawer');

        if (drawerState === 'open' && body) {
            body.classList.add('drawer-open');
            updateDrawerToggleArrow(true);
        }
    });

});
