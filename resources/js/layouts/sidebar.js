document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebar-toggle');
    const expandBtn = document.querySelector('.sidebar-expand-vertical');
    const body = document.body;

    if (!toggleBtn) return;

    // Restore state from localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        body.classList.add('sidebar-collapsed');
        updateToggleArrow(true);
    }

    function updateToggleArrow(collapsed) {
        const arrowSpan = toggleBtn.querySelector('.toggle-arrow');
        if (arrowSpan) {
            // Arrow points left when expanded (can collapse), points right when collapsed (can expand)
            arrowSpan.textContent = collapsed ? '→' : '←';
        }
        toggleBtn.title = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
    }

    function toggleSidebar() {
        const willBeCollapsed = !body.classList.contains('sidebar-collapsed');
        body.classList.toggle('sidebar-collapsed', willBeCollapsed);
        localStorage.setItem('sidebarCollapsed', willBeCollapsed);
        updateToggleArrow(willBeCollapsed);
    }

    toggleBtn.addEventListener('click', toggleSidebar);
    if (expandBtn) {
        expandBtn.addEventListener('click', toggleSidebar);
    }
});
