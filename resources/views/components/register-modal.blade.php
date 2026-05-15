{{--
    Register Confirmation + Success Modal
    Usage: include <x-register-modal /> in your register blade.
    Call window.registerModal.open(onConfirm) from your submit button.
    JS logic lives in resources/js/modals/register-modal.js
--}}

<style>
    /* ── Question mark pulse ── */
    @keyframes rm-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(17,17,17,0.20); }
        60%  { box-shadow: 0 0 0 10px rgba(17,17,17,0); }
        100% { box-shadow: 0 0 0 0 rgba(17,17,17,0); }
    }

    /* ── Circle pop ── */
    @keyframes rm-circle-pop {
        0%   { transform: scale(0.5); opacity: 0; }
        65%  { transform: scale(1.12); opacity: 1; }
        100% { transform: scale(1);    opacity: 1; }
    }

    /* ── Checkmark draw ── */
    @keyframes rm-check-draw {
        to { stroke-dashoffset: 0; }
    }

    /* ── Confetti dots ── */
    @keyframes rm-dot1 { to { transform: translate(-30px, -34px) scale(0); opacity: 0; } }
    @keyframes rm-dot2 { to { transform: translate( 32px, -30px) scale(0); opacity: 0; } }
    @keyframes rm-dot3 { to { transform: translate( 38px,  16px) scale(0); opacity: 0; } }
    @keyframes rm-dot4 { to { transform: translate(-36px,  18px) scale(0); opacity: 0; } }
    @keyframes rm-dot5 { to { transform: translate(  4px,  40px) scale(0); opacity: 0; } }
    @keyframes rm-dot6 { to { transform: translate(-10px,  40px) scale(0); opacity: 0; } }

    .rm-icon-pulse {
        animation: rm-pulse 1.8s ease-in-out infinite;
    }

    /* Circle wrapper — invisible until .rm-animate added by JS */
    .rm-circle-wrap {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: #111;
        display: flex; align-items: center; justify-content: center;
        position: relative;
        opacity: 0;
    }
    .rm-circle-wrap.rm-animate {
        animation: rm-circle-pop 0.45s cubic-bezier(.36,.07,.19,.97) forwards;
    }

    /* Checkmark path — drawn by animation once circle pops */
    .rm-check-path {
        stroke-dasharray: 60;
        stroke-dashoffset: 60;
    }
    .rm-circle-wrap.rm-animate .rm-check-path {
        animation: rm-check-draw 0.38s ease-out 0.38s forwards;
    }

    /* Confetti dots — positioned at center, burst outward via JS adding .rm-dot-active */
    .rm-dot {
        position: absolute;
        width: 7px; height: 7px;
        border-radius: 50%;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
    }
    .rm-dot-active.rm-dot1 { background: #111; animation: rm-dot1 0.55s ease-out 0.12s forwards; }
    .rm-dot-active.rm-dot2 { background: #555; animation: rm-dot2 0.55s ease-out 0.18s forwards; }
    .rm-dot-active.rm-dot3 { background: #111; animation: rm-dot3 0.55s ease-out 0.08s forwards; }
    .rm-dot-active.rm-dot4 { background: #888; animation: rm-dot4 0.55s ease-out 0.22s forwards; }
    .rm-dot-active.rm-dot5 { background: #111; animation: rm-dot5 0.55s ease-out 0.15s forwards; }
    .rm-dot-active.rm-dot6 { background: #555; animation: rm-dot6 0.55s ease-out 0.20s forwards; }
</style>

{{-- Backdrop --}}
<div id="register-modal-backdrop"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:999;"
     onclick="window.registerModal.cancel()">
</div>

{{-- Step 1: Confirmation Modal --}}
<div id="register-modal-confirm"
     style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
            background:#fff; border-radius:12px; padding:2rem; width:100%; max-width:420px;
            z-index:1000; box-shadow:0 20px 60px rgba(0,0,0,0.2); box-sizing:border-box;">

    <div style="text-align:center; margin-bottom:1.5rem;">

        {{-- Pulsing question mark icon --}}
        <div class="rm-icon-pulse"
             style="width:64px; height:64px; border-radius:50%; background:#f0f0f0;
                    display:flex; align-items:center; justify-content:center; margin:0 auto 1.1rem;">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#111" stroke-width="1.5"/>
                <path d="M9.5 9.5C9.5 7.567 10.843 6 12 6c1.38 0 2.5 1.12 2.5 2.5
                         0 1.2-.7 2-1.5 2.5-.6.37-1 .93-1 1.5v.5"
                      stroke="#111" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="16.5" r="0.9" fill="#111"/>
            </svg>
        </div>

        <h2 style="font-size:1.1rem; font-weight:600; color:#111; margin:0 0 0.4rem;">
            Are you sure you wish to proceed?
        </h2>
        <p style="font-size:0.85rem; color:#666; margin:0;">
            Make sure all information entered is correct before creating the account.
        </p>
    </div>

    <div style="display:flex; gap:0.75rem;">
        <button onclick="window.registerModal.cancel()"
                style="flex:1; padding:0.65rem 1rem; border:1.5px solid #111; border-radius:8px;
                       background:#fff; color:#111; font-size:0.9rem; font-weight:500;
                       cursor:pointer; transition:background 0.15s;"
                onmouseover="this.style.background='#f5f5f5'"
                onmouseout="this.style.background='#fff'">
            Cancel
        </button>
        <button onclick="window.registerModal.confirm()"
                style="flex:1; padding:0.65rem 1rem; border:1.5px solid #111; border-radius:8px;
                       background:#111; color:#fff; font-size:0.9rem; font-weight:500;
                       cursor:pointer; transition:background 0.15s;"
                onmouseover="this.style.background='#333'"
                onmouseout="this.style.background='#111'">
            Confirm
        </button>
    </div>
</div>

{{-- Step 2: Success Modal --}}
<div id="register-modal-success"
     style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
            background:#fff; border-radius:12px; padding:2rem; width:100%; max-width:420px;
            z-index:1000; box-shadow:0 20px 60px rgba(0,0,0,0.2); box-sizing:border-box;">

    <div style="text-align:center; margin-bottom:1.5rem;">

        {{-- Animated checkmark with confetti burst --}}
        <div style="position:relative; width:64px; height:64px; margin:0 auto 1.1rem;">
            <div class="rm-dot rm-dot1"></div>
            <div class="rm-dot rm-dot2"></div>
            <div class="rm-dot rm-dot3"></div>
            <div class="rm-dot rm-dot4"></div>
            <div class="rm-dot rm-dot5"></div>
            <div class="rm-dot rm-dot6"></div>

            <div class="rm-circle-wrap" id="rm-circle">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none"
                     stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline class="rm-check-path" points="4 12 9 17 20 6"/>
                </svg>
            </div>
        </div>

        <h2 style="font-size:1.1rem; font-weight:600; color:#111; margin:0 0 0.75rem;">
            Account registered successfully.
        </h2>
        <p style="font-size:0.875rem; color:#555; margin:0; line-height:1.6;">
            Please remind the new user to verify their email, on first sign-in.
        </p>
    </div>

    <button onclick="window.registerModal.done()"
            style="width:100%; padding:0.65rem 1rem; border:1.5px solid #111; border-radius:8px;
                   background:#111; color:#fff; font-size:0.9rem; font-weight:500;
                   cursor:pointer; transition:background 0.15s;"
            onmouseover="this.style.background='#333'"
            onmouseout="this.style.background='#111'">
        Confirm
    </button>
</div>
