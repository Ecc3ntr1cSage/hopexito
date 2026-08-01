@section('title', $product->title . ' | HopeXito')
@section('thumbnail', $product->product_card_image)

@php
    $initialColor = $colors->contains($product->preview_color)
        ? $product->preview_color
        : ($colors->first() ?? 'White');
    $hasBack = filled($product->product_image_2);
    $isShirt = $product->category === 'Shirt';
    $tags = collect(explode(',', (string) $product->tags))->map(fn ($tag) => trim($tag))->filter();
    $sizeChart = $isShirt
        ? [
            'columns' => ['Size', 'Shoulder', 'Chest', 'Sleeve', 'Length'],
            'rows' => [
                ['XS', '15"', '36"', '7.5"', '26"'],
                ['S', '16"', '38"', '8"', '27"'],
                ['M', '17"', '40"', '8.5"', '28"'],
                ['L', '18"', '42"', '9"', '29"'],
                ['XL', '19"', '44"', '9.5"', '30"'],
                ['2XL', '20"', '46"', '10"', '31"'],
            ],
        ]
        : [
            'columns' => ['Size', 'Width', 'Length'],
            'rows' => [
                ['S', '54cm', '66cm'],
                ['M', '57cm', '69cm'],
                ['L', '60cm', '75cm'],
                ['XL', '64cm', '76cm'],
                ['2XL', '68cm', '78cm'],
            ],
        ];
@endphp

<x-app-layout>
    <x-jet-whatsapp-contact />

    <main class="product-page" x-data="{
        preview: 'front',
        size: '',
        color: '{{ $initialColor }}',
        quantity: 1,
        sizeChartOpen: false,
        variants: @js($variantData),
        fallbackFront: '{{ $product->product_image }}',
        fallbackBack: '{{ $product->product_image_2 }}',
        image(side) {
            const selected = this.variants[this.color] || {};
            return side === 'back' ? (selected.back || this.fallbackBack) : (selected.front || this.fallbackFront);
        },
        increment() { this.quantity = Math.min(99, this.quantity + 1); },
        decrement() { this.quantity = Math.max(1, this.quantity - 1); }
    }">
        <x-jet-session-message />

        <section class="product-hero product-container">
            <div class="product-breadcrumb product-reveal">
                <a href="{{ route('discover') }}">Marketplace</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('people', $product->shopname) }}">{{ $product->shopname }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $product->category }}</span>
            </div>

            <div class="product-layout">
                <div class="product-gallery product-reveal">
                    <div class="product-gallery-heading">
                        <span class="product-eyebrow"><i></i> Edition {{ str_pad((string) $product->id, 3, '0', STR_PAD_LEFT) }}</span>
                        <span class="product-gallery-count">{{ $product->category }} / {{ $hasBack ? '02 views' : '01 view' }}</span>
                    </div>

                    <div class="product-visual-shell">
                        <div class="product-visual-core">
                            <div class="product-visual-grid" aria-hidden="true"></div>
                            <span class="product-visual-label">HOPEXITO / OBJECT STUDY</span>
                            <span class="product-visual-coordinate">{{ $product->category }}<br>01.{{ str_pad((string) $product->id, 2, '0', STR_PAD_LEFT) }}</span>
                            <img class="product-hero-image" x-bind:src="image(preview)" x-bind:alt="'{{ $product->title }} ' + color + ' ' + preview + ' view'">
                            <span class="product-visual-mark" aria-hidden="true">HX</span>
                        </div>
                    </div>

                    <div class="product-gallery-footer">
                        <div class="product-thumbnails" aria-label="Product views">
                            <button type="button" class="product-thumbnail" :class="preview === 'front' ? 'is-active' : ''" @click="preview = 'front'" aria-label="Show front view">
                                <span>01</span>
                                <img x-bind:src="image('front')" alt="">
                            </button>
                            @if ($hasBack)
                                <button type="button" class="product-thumbnail" :class="preview === 'back' ? 'is-active' : ''" @click="preview = 'back'" aria-label="Show back view">
                                    <span>02</span>
                                    <img x-bind:src="image('back')" alt="">
                                </button>
                            @endif
                        </div>
                        <div class="product-specs">
                            <span><i class="spec-dot spec-dot-teal"></i> Made to order</span>
                            <span><i class="spec-dot spec-dot-pink"></i> {{ $isShirt ? '100% cotton' : 'Heavyweight cotton' }}</span>
                        </div>
                    </div>
                </div>

                <aside class="product-purchase product-reveal product-reveal-delay">
                    <div class="product-purchase-topline">
                        <span>Independent design / {{ $product->category }}</span>
                        <span class="product-stock"><i></i> Available</span>
                    </div>

                    <div class="product-heading">
                        <p class="product-kicker">From the studio of <a href="{{ route('people', $product->shopname) }}">{{ $product->shopname }}</a></p>
                        <h1>{{ $product->title }}</h1>
                        <p class="product-description">A considered everyday piece, printed when you order and made to stay in rotation.</p>
                    </div>

                    <div class="product-price-row">
                        <div>
                            <span class="product-price">RM{{ number_format($product->price, 2) }}</span>
                            @if (Auth::check() && $product->isOwnedBy(Auth::user()))
                                <span class="product-member-price">RM{{ number_format($product->price * .85, 2) }} member price</span>
                            @endif
                        </div>
                        <span class="product-shipping-note">Ships in 3-5 days</span>
                    </div>

                    @if ($tags->isNotEmpty())
                        <div class="product-tags" aria-label="Product tags">
                            @foreach ($tags as $tag)
                                <span>{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('cart.store') }}" method="POST" class="product-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" :value="quantity">

                        <div class="product-choice-block">
                            <div class="product-choice-heading">
                                <span><b>01</b> Select size</span>
                                <button type="button" class="product-text-button" @click="sizeChartOpen = true">Size guide <span aria-hidden="true">&nearr;</span></button>
                            </div>
                            <div class="product-size-grid">
                                @foreach (config('catalog.sizes') as $sizeOption)
                                    <label class="product-size-option" :class="size === '{{ $sizeOption }}' ? 'is-selected' : ''">
                                        <input type="radio" name="size" value="{{ $sizeOption }}" x-model="size">
                                        <span>{{ $sizeOption }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('size') <p class="product-form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="product-choice-block">
                            <div class="product-choice-heading">
                                <span><b>02</b> Select color</span>
                                <span class="product-selection-value" x-text="color"></span>
                            </div>
                            <div class="product-color-grid">
                                @foreach ($colors as $colorOption)
                                    @php($colorKey = strtolower($colorOption))
                                    <label class="product-color-option" :class="color === '{{ $colorOption }}' ? 'is-selected' : ''">
                                        <input type="radio" name="color" value="{{ $colorOption }}" x-model="color">
                                        <span class="product-color-swatch product-color-{{ $colorKey }}"></span>
                                        <span>{{ $colorOption }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('color') <p class="product-form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="product-form-bottom">
                            <div class="product-quantity" aria-label="Quantity">
                                <button type="button" @click="decrement" aria-label="Decrease quantity">&minus;</button>
                                <output x-text="quantity"></output>
                                <button type="button" @click="increment" aria-label="Increase quantity">&plus;</button>
                            </div>
                            <span class="product-quantity-label">Quantity</span>
                        </div>

                        <div class="product-actions">
                            <button type="submit" name="add_to_cart" class="product-action product-action-primary group">
                                <span>Add to bag</span>
                                <span class="product-action-icon" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none"><path d="M4 10h11M10 5l5 5-5 5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                            </button>
                            <button type="submit" name="buy_now" class="product-action product-action-secondary group">
                                <span>Buy now</span>
                                <span class="product-action-icon" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none"><path d="M4 10h11M10 5l5 5-5 5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                            </button>
                        </div>
                        <p class="product-form-note"><span aria-hidden="true">+</span> Free delivery over RM150 <span aria-hidden="true">&middot;</span> Easy returns within 14 days</p>
                    </form>
                </aside>
            </div>
        </section>

        <section x-cloak x-show="sizeChartOpen" x-transition.opacity class="product-modal-backdrop" @click.self="sizeChartOpen = false" @keydown.escape.window="sizeChartOpen = false" role="presentation">
            <div class="product-modal" role="dialog" aria-modal="true" aria-labelledby="size-chart-title">
                <div class="product-modal-header">
                    <div>
                        <span class="product-eyebrow"><i></i> Fit notes</span>
                        <h2 id="size-chart-title">Size guide</h2>
                    </div>
                    <button type="button" class="product-modal-close" @click="sizeChartOpen = false" aria-label="Close size guide">&times;</button>
                </div>
                <table class="product-size-table">
                    <thead><tr>@foreach ($sizeChart['columns'] as $column)<th>{{ $column }}</th>@endforeach</tr></thead>
                    <tbody>
                        @foreach ($sizeChart['rows'] as $row)
                            <tr>@foreach ($row as $index => $value)<td class="{{ $index === 0 ? 'is-label' : '' }}">{{ $value }}</td>@endforeach</tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="product-modal-note">Measurements are taken flat. For a relaxed fit, choose one size up.</p>
            </div>
        </section>

        <section class="product-recommendations product-section">
            <div class="product-section-heading product-container">
                <div>
                    <span class="product-eyebrow"><i></i> The maker's shelf</span>
                    <h2>More from {{ $product->shopname }}</h2>
                </div>
                <a class="product-section-link" href="{{ route('people', $product->shopname) }}">View profile <span aria-hidden="true">&nearr;</span></a>
            </div>
            <div class="product-recommendation-track product-container">
                @forelse ($products as $related)
                    <a href="{{ route('product.show', $related->slug) }}" class="related-product" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                        <div class="related-product-image">
                            <img src="{{ $related->product_card_image }}" alt="{{ $related->title }}" :class="hover ? 'is-hidden' : ''">
                            @if ($related->product_card_hover_image)
                                <img src="{{ $related->product_card_hover_image }}" alt="" class="related-product-back" :class="hover ? 'is-visible' : ''">
                            @endif
                            <span class="related-product-arrow" aria-hidden="true">&nearr;</span>
                        </div>
                        <div class="related-product-meta"><div><span>{{ $related->category }}</span><h3>{{ $related->title }}</h3></div><strong>RM{{ number_format($related->price, 2) }}</strong></div>
                    </a>
                @empty
                    <p class="product-empty">More editions from this designer are on the way.</p>
                @endforelse
            </div>
        </section>

        <section class="product-discover product-section">
            <div class="product-section-heading product-container">
                <div>
                    <span class="product-eyebrow"><i></i> Keep looking</span>
                    <h2>Designed elsewhere</h2>
                </div>
                <a class="product-section-link" href="{{ route('discover') }}">Explore marketplace <span aria-hidden="true">&nearr;</span></a>
            </div>
            <div class="product-recommendation-track product-container">
                @forelse ($discover as $item)
                    <a href="{{ route('product.show', $item->slug) }}" class="related-product" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                        <div class="related-product-image">
                            <img src="{{ $item->product_card_image }}" alt="{{ $item->title }}" :class="hover ? 'is-hidden' : ''">
                            @if ($item->product_card_hover_image)
                                <img src="{{ $item->product_card_hover_image }}" alt="" class="related-product-back" :class="hover ? 'is-visible' : ''">
                            @endif
                            <span class="related-product-arrow" aria-hidden="true">&nearr;</span>
                        </div>
                        <div class="related-product-meta"><div><span>{{ $item->category }}</span><h3>{{ $item->title }}</h3></div><strong>RM{{ number_format($item->price, 2) }}</strong></div>
                    </a>
                @empty
                    <p class="product-empty">The marketplace is gathering its next set of pieces.</p>
                @endforelse
            </div>
        </section>
    </main>
</x-app-layout>
