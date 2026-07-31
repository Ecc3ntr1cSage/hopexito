@props([
    'initialMode' => 'login',
    'forceOpen' => false,
])

@php
    $requestedMode = request()->query('auth');
    $open = $forceOpen || $requestedMode === 'login';
@endphp

<div
    x-data="{
        open: @js($open),
        mode: @js($initialMode),
        openAuth(detail = {}) {
            this.mode = detail.mode || 'login';
            this.open = true;
            this.$nextTick(() => this.$refs.authDialog?.focus());
        },
        closeAuth() {
            this.open = false;
        }
    }"
    x-on:open-auth.window="openAuth($event.detail)"
    x-on:keydown.escape.window="if (open) closeAuth()"
    x-effect="document.body.classList.toggle('auth-modal-open', open)"
    class="auth-modal-root"
>
    <div
        x-cloak
        x-show="open"
        x-transition:enter="auth-modal-enter"
        x-transition:enter-start="auth-modal-enter-start"
        x-transition:enter-end="auth-modal-enter-end"
        x-transition:leave="auth-modal-leave"
        x-transition:leave-start="auth-modal-leave-start"
        x-transition:leave-end="auth-modal-leave-end"
        class="auth-modal-backdrop"
        role="presentation"
        @click.self="closeAuth()"
    >
        <section
            x-ref="authDialog"
            x-show="open"
            x-transition:enter="auth-modal-card-enter"
            x-transition:enter-start="auth-modal-card-start"
            x-transition:enter-end="auth-modal-card-end"
            x-transition:leave="auth-modal-card-leave"
            x-transition:leave-start="auth-modal-card-start"
            x-transition:leave-end="auth-modal-card-leave-end"
            class="auth-modal-card"
            role="dialog"
            aria-modal="true"
            aria-labelledby="auth-modal-title"
            tabindex="-1"
        >
            <div class="auth-modal-aside" aria-hidden="true">
                <span class="auth-modal-aside-kicker">HOPEXITO / 01</span>
                <div class="auth-modal-aside-mark">H<span>X</span></div>
                <p>Make something worth wearing.</p>
                <span class="auth-modal-aside-rule"></span>
                <span class="auth-modal-aside-note">A home for independent ideas.</span>
            </div>

            <div class="auth-modal-main">
                <div class="auth-modal-topline">
                    <span class="auth-modal-kicker">Welcome back</span>
                    <button type="button" class="auth-modal-close" @click="closeAuth()" aria-label="Close authentication dialog">Close</button>
                </div>

                <div class="auth-modal-heading">
                    <h1 id="auth-modal-title">
                        <span x-show="mode === 'login'">Log in to HopeXito</span>
                        <span x-show="mode === 'register'" x-cloak>Create your HopeXito account</span>
                    </h1>
                    <p x-show="mode === 'login'">Pick up where you left off.</p>
                    <p x-show="mode === 'register'" x-cloak>Join the marketplace before you place an order.</p>
                </div>

                @if (session('status'))
                    <div class="auth-modal-status" role="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-modal-errors" role="alert">
                        <strong>Check the highlighted details.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form x-show="mode === 'login'" method="POST" action="{{ route('login') }}" class="auth-modal-form">
                    @csrf
                    <div class="auth-field">
                        <label for="auth-login-email">Email</label>
                        <input id="auth-login-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                        @error('email') <span class="auth-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="auth-field">
                        <div class="auth-field-label-row">
                            <label for="auth-login-password">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">Forgot password?</a>
                            @endif
                        </div>
                        <input id="auth-login-password" type="password" name="password" autocomplete="current-password" required>
                        @error('password') <span class="auth-field-error">{{ $message }}</span> @enderror
                    </div>

                    <label class="auth-check" for="auth-remember-me">
                        <input id="auth-remember-me" type="checkbox" name="remember">
                        <span>Keep me signed in</span>
                    </label>

                    <button type="submit" class="auth-submit">Log in <span aria-hidden="true">↗</span></button>
                </form>

                <form x-show="mode === 'register'" x-cloak method="POST" action="{{ route('register.store') }}" class="auth-modal-form">
                    @csrf
                    <div class="auth-field">
                        <label for="auth-register-name">Name</label>
                        <input id="auth-register-name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
                        @error('name') <span class="auth-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="auth-field">
                        <label for="auth-register-email">Email</label>
                        <input id="auth-register-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email') <span class="auth-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="auth-field-pair">
                        <div class="auth-field">
                            <label for="auth-register-password">Password</label>
                            <input id="auth-register-password" type="password" name="password" autocomplete="new-password" required>
                        </div>
                        <div class="auth-field">
                            <label for="auth-register-password-confirmation">Confirm password</label>
                            <input id="auth-register-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                        </div>
                    </div>
                    @error('password') <span class="auth-field-error">{{ $message }}</span> @enderror

                    <button type="submit" class="auth-submit">Create account <span aria-hidden="true">&rarr;</span></button>
                </form>

                <p class="auth-modal-switch" x-show="mode === 'login'">
                    New to HopeXito?
                    <button type="button" @click="mode = 'register'">Create an account</button>
                </p>
                <p class="auth-modal-switch" x-show="mode === 'register'" x-cloak>
                    Already have an account?
                    <button type="button" @click="mode = 'login'">Log in</button>
                </p>
            </div>
        </section>
    </div>
</div>
