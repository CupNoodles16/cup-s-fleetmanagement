/* global window, document */
window.registerModal = (function () {

    var _onConfirm = null;

    /* ── DOM getters ── */
    function getBackdrop()     { return document.getElementById('register-modal-backdrop'); }
    function getConfirmModal() { return document.getElementById('register-modal-confirm');  }
    function getSuccessModal() { return document.getElementById('register-modal-success');  }

    /* ── Helpers ── */
    function hideAll() {
        var els = [getBackdrop(), getConfirmModal(), getSuccessModal()];
        els.forEach(function (el) { if (el) el.style.display = 'none'; });
    }

    function triggerSuccessAnimation() {
        var circle = document.getElementById('rm-circle');
        var dots   = document.querySelectorAll('.rm-dot');

        if (circle) {
            // Force reflow so animation restarts each time the modal opens
            circle.classList.remove('rm-animate');
            void circle.offsetWidth;
            circle.classList.add('rm-animate');
        }

        if (dots.length) {
            dots.forEach(function (d) { d.classList.remove('rm-dot-active'); });
            void dots[0].offsetWidth;
            dots.forEach(function (d) { d.classList.add('rm-dot-active'); });
        }
    }

    /* ── Public API ── */
    return {

        /**
         * Open the confirmation modal.
         * @param {Function} onConfirm  Called after user clicks Confirm on the success modal.
         */
        open: function (onConfirm) {
            _onConfirm = onConfirm || null;
            hideAll();
            var backdrop = getBackdrop();
            var confirm  = getConfirmModal();
            if (backdrop) backdrop.style.display = 'block';
            if (confirm)  confirm.style.display  = 'block';
        },

        /** Close all modals without submitting. */
        cancel: function () {
            hideAll();
        },

        /** Step 1 → Step 2: hide confirm, show success + fire animations. */
        confirm: function () {
            var confirm = getConfirmModal();
            var success = getSuccessModal();
            if (confirm) confirm.style.display = 'none';
            if (success) success.style.display = 'block';
            triggerSuccessAnimation();
        },

        /** Step 2 done: close everything and call the submit callback. */
        done: function () {
            hideAll();
            if (typeof _onConfirm === 'function') _onConfirm();
        },
    };

})();
