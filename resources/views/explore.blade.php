@section('title', 'HopeXito — made by people with something to say')

<x-app-layout>
    <div class="landing-shell">
        <section class="landing-hero">
            <div class="landing-container">
                <div class="landing-hero-copy">
                    <p class="eyebrow"><span class="eyebrow-dot"></span> A community for independent ideas</p>
                    <h1>Make something<br><em>worth finding.</em></h1>
                    <p class="landing-lede">HopeXito is where makers turn their point of view into products — and people discover what feels like them.</p>
                    <div class="landing-actions">
                        <a href="{{ route('shop.all') }}" class="button button-primary">Explore the marketplace <span aria-hidden="true">↗</span></a>
                        @auth
                            <a href="{{ route('product.create') }}" class="button button-quiet">Start creating</a>
                        @else
                            <a href="{{ route('explore', ['auth' => 'login']) }}" class="button button-quiet" @click.prevent="$dispatch('open-auth', { mode: 'login' })">Log in to start creating</a>
                        @endauth
                    </div>
                    <div class="landing-proof" aria-label="HopeXito community highlights">
                        <div class="proof-avatars">
                            @foreach ($users->take(3) as $user)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                            @endforeach
                            @if ($users->isEmpty())
                                <span class="proof-avatar-fallback">H</span><span class="proof-avatar-fallback">X</span><span class="proof-avatar-fallback">O</span>
                            @endif
                        </div>
                        <p><strong>{{ $users->count() ? 'New work, every day.' : 'Your next idea belongs here.' }}</strong><br>Made by the HopeXito community.</p>
                    </div>
                </div>

                <div class="hero-stage" aria-label="A preview of community-made designs">
                    <div class="stage-note stage-note-top">01 / discover</div>
                    <div class="stage-grid-lines" aria-hidden="true"></div>
                    @forelse ($products->take(3) as $index => $product)
                        <a href="{{ route('product.show', $product->slug) }}" class="hero-product hero-product-{{ $index + 1 }}">
                            <img src="{{ $product->product_image }}" alt="{{ $product->title }} by {{ $product->shopname }}" />
                            <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </a>
                    @empty
                        <div class="hero-product hero-product-1 placeholder-product"><span>01</span><b>YOUR<br>IDEA</b></div>
                        <div class="hero-product hero-product-2 placeholder-product"><span>02</span><b>YOUR<br>VOICE</b></div>
                        <div class="hero-product hero-product-3 placeholder-product"><span>03</span><b>YOUR<br>WORLD</b></div>
                    @endforelse
                    <div class="stage-note stage-note-bottom">people-made / 2026</div>
                    <div class="stage-orbit" aria-hidden="true">✳</div>
                </div>
            </div>
        </section>

        <section class="discovery-section" id="discover">
            <div class="landing-container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">02 / the latest finds</p>
                        <h2>Worth a closer look.</h2>
                    </div>
                    <a href="{{ route('shop.all') }}" class="text-link">View all products <span aria-hidden="true">→</span></a>
                </div>
                <div class="product-grid">
                    @forelse ($products->take(4) as $product)
                        <a href="{{ route('product.show', $product->slug) }}" class="product-card">
                            <div class="product-card-image">
                                <img src="{{ $product->product_image }}" alt="{{ $product->title }}" loading="lazy" />
                                <span class="product-arrow" aria-hidden="true">↗</span>
                            </div>
                            <div class="product-card-meta">
                                <div>
                                    <h3>{{ $product->title }}</h3>
                                    <p>by {{ $product->shopname }}</p>
                                </div>
                                <strong>RM{{ number_format($product->price, 2) }}</strong>
                            </div>
                        </a>
                    @empty
                        <div class="empty-state">The first drop is on its way. Be the maker who starts it.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="maker-section">
            <div class="landing-container maker-panel">
                <div>
                    <p class="eyebrow eyebrow-dark">03 / for makers</p>
                    <h2>Your taste is<br><em>the product.</em></h2>
                </div>
                <div class="maker-copy">
                    <p>Upload a design, build your shop, and let the right people find it. No gatekeeping. Just a place to make your thing real.</p>
                    @auth
                        <a href="{{ route('product.create') }}" class="button button-dark">Create your first product <span aria-hidden="true">↗</span></a>
                    @else
                        <a href="{{ route('explore', ['auth' => 'login']) }}" class="button button-dark" @click.prevent="$dispatch('open-auth', { mode: 'login' })">Log in to start creating <span aria-hidden="true">↗</span></a>
                    @endauth
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
