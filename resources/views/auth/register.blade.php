@extends('layouts.guest')

@section('title', 'Create Dispatcher Account')

@section('content')
<div class="register-page">

    {{-- Left branding panel --}}
    <aside class="register-panel-left">
        <div class="register-brand">
            <div class="register-brand-mark">
                <div class="register-brand-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 3h15v11H1zM16 8h4l3 3v5h-7V8z"/>
                        <circle cx="5.5" cy="17.5" r="2.5"/>
                        <circle cx="18.5" cy="17.5" r="2.5"/>
                    </svg>
                </div>
                <span class="register-brand-name">Cupnoodles</span>
            </div>

            <h2 class="register-tagline">
                Built for those<br />
                <strong>who run the board.</strong>
            </h2>
            <p class="register-sub-tagline">
                Dispatcher accounts have full access to load assignment, order management, and real-time fleet visibility.
            </p>
        </div>

        {{-- Role info card --}}
        <div class="register-role-info">
            <div class="register-role-label">Account Type</div>
            <div class="register-role-title">Dispatcher</div>
            <div class="register-role-desc">
                Manages daily dispatch operations across all active loads and drivers.
            </div>
            <div class="register-role-permissions">
                <div class="register-role-permission">Create and confirm orders</div>
                <div class="register-role-permission">Assign and reassign loads</div>
                <div class="register-role-permission">Monitor live driver locations</div>
                <div class="register-role-permission">Generate and send invoices</div>
            </div>
        </div>
    </aside>

    {{-- Right form panel --}}
    <main class="register-panel-right">
        <div class="register-box">

            <div class="register-form-header">
                <h1>Create Account</h1>
                <p>Register a new dispatcher profile</p>
                <div class="register-form-divider"></div>
            </div>

            {{-- Role pill --}}
            <div class="register-role-pill">
                <span class="register-role-pill-dot"></span>
                Dispatcher Access
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="register-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Role hardcoded, not exposed --}}
                <input type="hidden" name="role" value="dispatcher" />

                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input
                        class="form-input @error('name') is-invalid @enderror"
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. Cup Noodles"
                        autocomplete="name"
                        required
                        autofocus
                    />
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input
                        class="form-input @error('email') is-invalid @enderror"
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@company.com"
                        autocomplete="email"
                        required
                    />
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input
                        class="form-input @error('password') is-invalid @enderror"
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        required
                    />
                    <p class="form-hint">Minimum 8 characters.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        required
                    />
                </div>

                <button type="submit" class="btn-register">
                    Create Account
                    <svg viewBox="0 0 24 24">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

        </div>
    </main>

</div>
@endsection
