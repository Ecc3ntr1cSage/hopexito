@section('title', 'Profile settings | HopeXito')

<x-app-layout>
    <main class="settings-page">
        <div class="settings-grain" aria-hidden="true"></div>

        <header class="settings-hero">
            <div class="settings-container">
                <div class="settings-topline settings-reveal">
                    <span class="settings-kicker"><i></i> Account / private workspace</span>
                    <span class="settings-index">01 — 04</span>
                </div>

                <div class="settings-hero-grid">
                    <div class="settings-title-block settings-reveal settings-delay-one">
                        <span class="settings-eyebrow">Your presence, tuned</span>
                        <h1>Shape the way<br><em>you appear.</em></h1>
                        <p>Keep your public identity, delivery details, and creator tools in one quiet place.</p>
                    </div>

                    <div class="settings-identity-shell settings-reveal settings-delay-two">
                        <div class="settings-identity-card">
                            <div class="settings-identity-label"><span>Current account</span><b>LIVE</b></div>
                            <div class="settings-identity-main">
                                <div class="settings-avatar-shell">
                                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                                </div>
                                <div>
                                    <h2>{{ Auth::user()->name }}</h2>
                                    <p>{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                            <a class="settings-identity-link" href="{{ route('people', Auth::user()->name) }}">
                                <span>View public profile</span><b aria-hidden="true">&nearr;</b>
                            </a>
                        </div>
                    </div>
                </div>

                <nav class="settings-anchor-nav settings-reveal settings-delay-three" aria-label="Profile settings sections">
                    <a href="#identity"><span>01</span> Identity</a>
                    <a href="#security"><span>02</span> Security</a>
                    <a href="#wallet"><span>03</span> Wallet</a>
                </nav>
            </div>
        </header>

        <div class="settings-container settings-body">
            <x-jet-session-message />

            <div class="settings-layout">
                <aside class="settings-rail settings-reveal settings-delay-two">
                    <div class="settings-rail-card">
                        <span class="settings-rail-label">Workspace map</span>
                        <p>Small adjustments compound into a profile people remember.</p>
                        <div class="settings-rail-line"><span></span></div>
                        <span class="settings-rail-note">Changes save to your account</span>
                    </div>
                    <a class="settings-rail-link" href="{{ route('people', Auth::user()->name) }}">
                        <span>Open public view</span><b aria-hidden="true">&nearr;</b>
                    </a>
                </aside>

                <div class="settings-main">
                    <section id="identity" class="settings-section settings-reveal settings-delay-one">
                        <div class="settings-section-heading">
                            <div>
                                <span class="settings-section-number">01 / Identity</span>
                                <h2>Make it unmistakably yours.</h2>
                            </div>
                            <p>Name, contact, and delivery details used across your HopeXito account.</p>
                        </div>
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('profile.update-profile-information-form')
                        @endif
                    </section>

                    <section id="security" class="settings-section settings-reveal settings-delay-two">
                        <div class="settings-section-heading">
                            <div>
                                <span class="settings-section-number">02 / Security</span>
                                <h2>Keep the door yours.</h2>
                            </div>
                            <p>A strong password keeps your work, orders, and earnings close.</p>
                        </div>
                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                            @livewire('profile.update-password-form')
                        @endif
                    </section>

                    <section id="wallet" class="settings-section settings-section-wallet settings-reveal settings-delay-two">
                        <div class="settings-section-heading">
                            <div>
                                <span class="settings-section-number">03 / Wallet</span>
                                <h2>See the work move.</h2>
                            </div>
                            <p>Track your balance, commissions, and withdrawal history.</p>
                        </div>
                        <div class="settings-wallet-slot">@livewire('wallet')</div>
                    </section>

                    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                        <section class="settings-section settings-danger-section settings-reveal settings-delay-three">
                            <div class="settings-section-heading">
                                <div>
                                    <span class="settings-section-number">04 / Exit</span>
                                    <h2>Leave with intention.</h2>
                                </div>
                                <p>Account deletion is permanent. Download anything you want to keep first.</p>
                            </div>
                            <div class="settings-livewire-card settings-danger-slot">@livewire('profile.delete-user-form')</div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
