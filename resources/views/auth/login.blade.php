@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')
<div class="login-page">

    {{-- Left branding panel --}}
    <aside class="login-panel-left">
        <div class="login-brand">
            <div class="login-brand-mark">
                <div class="login-brand-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 3h15v11H1zM16 8h4l3 3v5h-7V8z"/>
                        <circle cx="5.5" cy="17.5" r="2.5"/>
                        <circle cx="18.5" cy="17.5" r="2.5"/>
                    </svg>
                </div>
                <span class="login-brand-name">Cupnoodles</span>
            </div>

            <h2 class="login-tagline">
                Move cargo.<br />
                <strong>Command every mile.</strong>
            </h2>
            <p class="login-sub-tagline">
                Dispatch management built for operations teams who need clarity, speed, and full visibility across every load.
            </p>
        </div>

        <div class="login-stats">
            <div class="login-stat">
                <div class="login-stat-value">98%</div>
                <div class="login-stat-label">On-time rate</div>
            </div>
            <div class="login-stat">
                <div class="login-stat-value">4.8s</div>
                <div class="login-stat-label">Avg. assign time</div>
            </div>
            <div class="login-stat">
                <div class="login-stat-value">24/7</div>
                <div class="login-stat-label">Live tracking</div>
            </div>
        </div>
    </aside>

    {{-- Right form panel --}}
    <main class="login-panel-right">
        <div class="login-box">

            <div class="login-form-header">
                <h1>Sign In</h1>
                <p>Access your dispatch dashboard</p>
                <div class="login-form-divider"></div>
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="login-error">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Session status (e.g. after password reset) --}}
            @if (session('status'))
                <div class="login-error" style="background: #EEF5EE; border-color: #C0D8C0; color: #2A5E2A;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input
                        class="form-input @error('email') is-invalid @enderror"
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="cup-dispatch@company.com"
                        autocomplete="email"
                        required
                        autofocus
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
                        autocomplete="current-password"
                        required
                    />
                </div>

                <div class="login-form-footer">
                    <label class="login-remember">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="login-forgot">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn-login">
                    Sign in
                    <svg viewBox="0 0 24 24">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            <p class="login-footnote">
                CupNoodles &copy; {{ date('Y') }} &mdash; Internal use only
            </p>

        </div>
    </main>

</div>
@endsection


