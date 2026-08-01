@php
    $productCount = $products->total();
    $typeLabels = ['shirt' => 'Shirt', 'sweat' => 'Sweatshirt', 'hoodie' => 'Hoodie'];
@endphp

@section('title', 'Discover independent design | HopeXito')

<x-app-layout>
    <x-jet-whatsapp-contact />

    <main class="discover-page">
        <section class="discover-hero">
            <div class="discover-container">
                <div class="discover-hero-copy">
                    <p class="discover-eyebrow"><span></span> Marketplace / 001</p>
                    <h1>Find something with a <em>point of view.</em></h1>
                    <p class="discover-hero-lede">
                        Wearable ideas from people making things their own way. Browse the archive, find your signal,
                        and take it with you.
                    </p>
                    <div class="discover-hero-actions">
                        <a class="discover-primary-link" href="#archive">Browse the archive <span>↓</span></a>
                        <span class="discover-hero-note">Small runs / made to order</span>
                    </div>
                </div>

                <div class="discover-hero-index" aria-label="Marketplace summary">
                    <div class="discover-index-grid"></div>
                    <span class="discover-index-label">Field note</span>
                    <strong>People-made<br>for the in-between.</strong>
                    <span class="discover-index-mark">✳</span>
                    <div class="discover-index-footer">
                        <span>{{ str_pad((string) $productCount, 2, '0', STR_PAD_LEFT) }} editions</span>
                        <span>KL / 2026</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="discover-archive" id="archive">
            <div class="discover-container">
                <div class="discover-archive-heading">
                    <div>
                        <p class="discover-eyebrow"><span></span> 02 / The archive</p>
                        <h2>Objects with<br><em>something to say.</em></h2>
                    </div>
                    <p class="discover-archive-intro">
                        Every piece starts with an idea. Explore the latest designs from the HopeXito community.
                    </p>
                </div>

                <nav class="discover-filter-rail" aria-label="Browse product types">
                    <div class="discover-filter-links">
                        <a class="{{ $selectedType === null ? 'is-active' : '' }}" href="{{ route('discover') }}#archive"
                            @if ($selectedType === null) aria-current="page" @endif>
                            All <small>{{ $typeCounts->sum() }}</small>
                        </a>
                        @foreach ($typeLabels as $type => $label)
                            <a class="{{ $selectedType === $type ? 'is-active' : '' }}"
                                href="{{ route('discover', ['type' => $type]) }}#archive"
                                @if ($selectedType === $type) aria-current="page" @endif>
                                {{ $label }} <small>{{ $typeCounts->get($type, 0) }}</small>
                            </a>
                        @endforeach
                    </div>
                    <span class="discover-filter-status">{{ $selectedType ? $typeLabels[$selectedType] : 'All forms' }} <i></i></span>
                </nav>

                <div class="discover-grid">
                    @forelse ($products as $product)
                        <a href="{{ route('product.show', $product->slug) }}"
                            class="discover-card {{ $loop->first ? 'discover-card-featured' : '' }}">
                            <div class="discover-card-media">
                                <span class="discover-card-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="discover-card-arrow" aria-hidden="true">↗</span>

                                @if ($product->product_card_image)
                                    <img src="{{ $product->product_card_image }}" alt="{{ $product->title }}"
                                        class="discover-card-image" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif
                                    >
                                @endif

                                <span class="discover-card-side">Preview / {{ (int) $product->preview === 1 ? 'Back' : 'Front' }}</span>
                            </div>
                            <div class="discover-card-meta">
                                <div>
                                    <span class="discover-card-category">{{ $product->category }}</span>
                                    <h3>{{ $product->title }}</h3>
                                    <p>By {{ $product->shopname }}</p>
                                </div>
                                <strong>RM{{ number_format($product->price, 2) }}</strong>
                            </div>
                        </a>
                    @empty
                        <div class="discover-empty-state">
                            <span class="discover-eyebrow"><span></span> Archive quiet</span>
                            <h2>Nothing here yet.</h2>
                            <p>Check back soon for new work from the community.</p>
                        </div>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <nav class="discover-pagination" aria-label="Archive pages">
                        @if ($products->onFirstPage())
                            <span class="is-disabled">← Previous</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}">← Previous</a>
                        @endif

                        <span>Page {{ $products->currentPage() }} / {{ $products->lastPage() }}</span>

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}">Next →</a>
                        @else
                            <span class="is-disabled">Next →</span>
                        @endif
                    </nav>
                @endif
            </div>
        </section>
    </main>
</x-app-layout>
