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
                        <a href="{{ route('discover') }}" class="button button-primary">Discover the marketplace <span aria-hidden="true">↗</span></a>
                        @auth
                            <a href="{{ route('product.create') }}" class="button button-quiet">Start creating</a>
                        @else
                            <a href="{{ route('home', ['auth' => 'login']) }}" class="button button-quiet" @click.prevent="$dispatch('open-auth')">Log in to start creating</a>
                        @endauth
                    </div>
                </div>

                <div class="hero-stage" aria-label="A visual study of independent ideas">
                    <div class="stage-note stage-note-top">01 / discover</div>
                    <div class="stage-grid-lines" aria-hidden="true"></div>
                    <div class="hero-poster hero-poster-1">
                        <span class="hero-poster-index">01 / point of view</span>
                        <div class="hero-poster-signal hero-poster-signal-one" aria-hidden="true"></div>
                        <strong>MAKE<br>NOISE</strong>
                        <span class="hero-poster-caption">ideas with a pulse</span>
                    </div>
                    <div class="hero-poster hero-poster-2">
                        <span class="hero-poster-index">02 / signal study</span>
                        <div class="hero-poster-signal hero-poster-signal-two" aria-hidden="true"></div>
                        <strong>STAY<br>CURIOUS</strong>
                        <span class="hero-poster-caption">wear the unexpected</span>
                    </div>
                    <div class="hero-poster hero-poster-3">
                        <span class="hero-poster-index">03</span>
                        <div class="hero-poster-signal hero-poster-signal-three" aria-hidden="true"></div>
                        <strong>YOUR<br>WORLD</strong>
                    </div>
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
                    <a href="{{ route('discover') }}" class="text-link">View all products <span aria-hidden="true">→</span></a>
                </div>
                <div class="product-grid">
                    @forelse ($products->take(4) as $product)
                        <a href="{{ route('product.show', $product->slug) }}" class="product-card">
                            <div class="product-card-image">
                                <img src="{{ $product->product_card_image }}" alt="{{ $product->title }}" loading="lazy" />
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
                        <a href="{{ route('home', ['auth' => 'login']) }}" class="button button-dark" @click.prevent="$dispatch('open-auth')">Log in to start creating <span aria-hidden="true">↗</span></a>
                    @endauth
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
