@props([
    'id' => 'confirmation-modal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you wish to proceed with this action?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
])

<div x-data="{
    show: false,
    resolve: null,

    open() {
        this.show = true;
        return new Promise((resolve) => {
            this.resolve = resolve;
        });
    },

    confirm() {
        this.show = false;
        if (this.resolve) this.resolve(true);
    },

    cancel() {
        this.show = false;
        if (this.resolve) this.resolve(false);
    }
}"
x-on:keydown.escape.window="show && cancel()"
x-cloak>

    {{-- Modal Backdrop --}}
    <div x-show="show"
         x-transition:enter="modal-backdrop-enter"
         x-transition:enter-start="modal-backdrop-enter-start"
         x-transition:enter-end="modal-backdrop-enter-end"
         x-transition:leave="modal-backdrop-leave"
         x-transition:leave-start="modal-backdrop-leave-start"
         x-transition:leave-end="modal-backdrop-leave-end"
         class="modal-backdrop"
         @click="cancel()">
    </div>

    {{-- Modal Container --}}
    <div x-show="show"
         x-transition:enter="modal-container-enter"
         x-transition:enter-start="modal-container-enter-start"
         x-transition:enter-end="modal-container-enter-end"
         x-transition:leave="modal-container-leave"
         x-transition:leave-start="modal-container-leave-start"
         x-transition:leave-end="modal-container-leave-end"
         class="modal-container"
         role="dialog"
         aria-modal="true">

        <div class="modal-content">
            {{-- Icon --}}
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            {{-- Title --}}
            <h3 class="modal-title">{{ $title }}</h3>

            {{-- Message --}}
            <p class="modal-message">{{ $message }}</p>

            {{-- Buttons --}}
            <div class="modal-buttons">
                <button type="button" class="modal-btn-cancel" @click="cancel()">
                    {{ $cancelText }}
                </button>
                <button type="button" class="modal-btn-confirm" @click="confirm()">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>
