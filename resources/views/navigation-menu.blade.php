<nav x-data="{ open: false }" class="site-nav">
    <div class="site-nav-inner">
        <a href="{{ route('explore') }}" class="site-wordmark" aria-label="HopeXito home"><span>H</span>ope<span class="wordmark-x">X</span>ito</a>
        <div class="site-nav-links">
            <a href="{{ route('explore') }}#discover">Discover</a>
            <a href="{{ route('shop.all') }}">Marketplace</a>
            @auth
                <a href="{{ route('product.create') }}" class="nav-create">Create <span aria-hidden="true">↗</span></a>
            @else
                <a href="{{ route('explore') }}" class="nav-create">Log in to create <span aria-hidden="true">↗</span></a>
            @endauth
        </div>
        <div class="site-nav-account">
            @auth
                <a href="{{ route('people', Auth::user()->name) }}" class="nav-profile"><img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" /></a>
                @livewire('cart.cart-counter')
            @else
                <a href="{{ route('explore') }}">Log in</a>
                @livewire('cart.cart-counter')
            @endauth
        </div>
        <button @click="open = !open" class="site-nav-menu" :aria-expanded="open.toString()" aria-label="Toggle menu"><span></span><span></span></button>
    </div>
    <div x-show="open" x-cloak class="site-nav-mobile">
        <a href="{{ route('explore') }}#discover">Discover</a>
        <a href="{{ route('shop.all') }}">Marketplace</a>
        @auth
            <a href="{{ route('product.create') }}">Create a product <span aria-hidden="true">↗</span></a>
            <a href="{{ route('people', Auth::user()->name) }}">Your profile</a>
        @else
            <a href="{{ route('explore') }}">Log in to create <span aria-hidden="true">↗</span></a>
            <a href="{{ route('explore') }}">Log in</a>
        @endauth
    </div>
</nav>
