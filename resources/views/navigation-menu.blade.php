<nav x-data="{ open: false }" class="site-nav">
    <div class="site-nav-inner">
        <a href="{{ route('home') }}" class="site-wordmark" aria-label="HopeXito home"><span>H</span>ope<span class="wordmark-x">X</span>ito</a>
        <div class="site-nav-links">
            <a href="{{ route('discover') }}">Discover</a>
            @auth
                <a href="{{ route('product.create') }}" class="nav-create">Create <span aria-hidden="true">&nearr;</span></a>
            @else
                <a href="{{ route('home', ['auth' => 'login']) }}" class="nav-create" @click.prevent="$dispatch('open-auth', { mode: 'login' })">Create now <span aria-hidden="true">&nearr;</span></a>
            @endauth
        </div>
        <div class="site-nav-account">
            @auth
                <div class="site-account-menu" x-data="{ accountOpen: false }" @click.outside="accountOpen = false">
                    <button type="button" class="site-account-trigger" @click="accountOpen = !accountOpen" :aria-expanded="accountOpen.toString()" aria-label="Open account menu">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        <svg aria-hidden="true" viewBox="0 0 16 16" fill="none"><path d="m4 6 4 4 4-4" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <div x-cloak x-show="accountOpen" x-transition:enter="account-popover-enter" x-transition:enter-start="account-popover-start" x-transition:enter-end="account-popover-end" x-transition:leave="account-popover-leave" x-transition:leave-start="account-popover-end" x-transition:leave-end="account-popover-start" class="site-account-popover">
                        <span class="site-account-label">Account / {{ Auth::user()->name }}</span>
                        <a href="{{ route('people', Auth::user()->name) }}"><span>Public profile</span><b aria-hidden="true">&nearr;</b></a>
                        <a href="{{ route('profile.show') }}"><span>Profile settings</span><b aria-hidden="true">&nearr;</b></a>
                        <a href="{{ route('product.manage') }}"><span>Manage products</span><b aria-hidden="true">&nearr;</b></a>
                        <a href="{{ route('product.create') }}"><span>Create a product</span><b aria-hidden="true">&nearr;</b></a>
                        <form method="POST" action="{{ route('logout') }}" class="site-account-logout-form">
                            @csrf
                            <button type="submit"><span>Log out</span><b aria-hidden="true">&nearr;</b></button>
                        </form>
                    </div>
                </div>
                @livewire('cart.cart-counter')
            @else
                <a href="{{ route('home', ['auth' => 'login']) }}" @click.prevent="$dispatch('open-auth', { mode: 'login' })">Log in</a>
                @livewire('cart.cart-counter')
            @endauth
        </div>
        <button @click="open = !open" class="site-nav-menu" :aria-expanded="open.toString()" aria-label="Toggle menu"><span></span><span></span></button>
    </div>
    <div x-show="open" x-cloak class="site-nav-mobile">
        <a href="{{ route('discover') }}">Discover</a>
        @auth
            <a href="{{ route('product.create') }}">Create a product <span aria-hidden="true">&nearr;</span></a>
            <a href="{{ route('people', Auth::user()->name) }}">Your profile</a>
            <div class="site-nav-mobile-account">
                <span>Account</span>
                <a href="{{ route('profile.show') }}">Profile settings <span aria-hidden="true">&nearr;</span></a>
                <a href="{{ route('product.manage') }}">Manage products <span aria-hidden="true">&nearr;</span></a>
                <form method="POST" action="{{ route('logout') }}" class="site-nav-mobile-logout">
                    @csrf
                    <button type="submit">Log out <span aria-hidden="true">&nearr;</span></button>
                </form>
            </div>
        @else
            <a href="{{ route('home', ['auth' => 'login']) }}" @click.prevent="open = false; $dispatch('open-auth', { mode: 'login' })">Create now <span aria-hidden="true">&nearr;</span></a>
            <a href="{{ route('home', ['auth' => 'login']) }}" @click.prevent="open = false; $dispatch('open-auth', { mode: 'login' })">Log in</a>
        @endauth
    </div>
</nav>
