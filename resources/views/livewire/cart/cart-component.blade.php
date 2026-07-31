@section('title', 'Cart | HopeXito')

<div class="cart-page">
    <x-jet-session-message />
    <x-jet-whatsapp-contact />

    @if ($cart->count() === 0)
        <section class="cart-empty-shell">
            <div class="cart-kicker">Your selection</div>
            <div class="cart-empty-mark" aria-hidden="true">+</div>
            <h1>Your bag is waiting.</h1>
            <p>Discover something worth keeping close, then return here when you are ready.</p>
            <a class="cart-primary-action" href="{{ route('shop.all') }}">
                Explore the marketplace
                <span aria-hidden="true">&rarr;</span>
            </a>
        </section>
    @else
        <header class="cart-hero">
            <div>
                <div class="cart-kicker">HopeXito / bag</div>
                <h1>Your selections, considered.</h1>
                <p>{{ $cart->count() }} {{ $cart->count() === 1 ? 'piece' : 'pieces' }} ready for their next chapter.</p>
            </div>
            <a class="cart-text-link" href="{{ route('shop.all') }}">
                Continue shopping <span aria-hidden="true">&rarr;</span>
            </a>
        </header>

        <div class="cart-layout">
            <section class="cart-items-panel">
                <div class="cart-panel-heading">
                    <span>In your bag</span>
                    <span class="cart-panel-count">{{ str_pad($cart->count(), 2, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="cart-item-list">
                    @foreach ($cart as $item)
                        @php
                            $isAuthenticated = Auth::check();
                            $productName = $isAuthenticated ? $item->title : $item['name'];
                            $productImage = $isAuthenticated ? $item->cartProduct->product_image : $item['options']['product_image'];
                            $productSize = $isAuthenticated ? $item->size : $item['options']['size'];
                            $productColor = $isAuthenticated ? $item->color : $item['options']['color'];
                            $quantity = $isAuthenticated ? $item->quantity : $item['qty'];
                            $itemKey = $isAuthenticated ? $item->id : $item['rowId'];
                            $itemPrice = $isAuthenticated ? $item->price : $item['price'];
                            $itemSubtotal = $isAuthenticated ? $item->subtotal : $item['price'] * $item['qty'];
                        @endphp

                        <article class="cart-item-row">
                            @if ($isAuthenticated)
                                <a class="cart-item-image-wrap" href="{{ route('product.show', $item->cartProduct->slug) }}">
                                    <img class="cart-item-image" src="{{ $productImage }}" alt="{{ $productName }}">
                                </a>
                            @else
                                <div class="cart-item-image-wrap">
                                    <img class="cart-item-image" src="{{ $productImage }}" alt="{{ $productName }}">
                                </div>
                            @endif

                            <div class="cart-item-copy">
                                <div class="cart-item-eyebrow">Made for the everyday</div>
                                <h2>{{ $productName }}</h2>
                                <p class="cart-item-variant">{{ $productSize }} <span>/</span> {{ $productColor }}</p>
                                <button class="cart-remove-button" type="button" wire:click="destroyCart('{{ $itemKey }}')">
                                    Remove from bag
                                </button>
                            </div>

                            <div class="cart-item-controls">
                                <div class="cart-quantity-control" aria-label="Quantity">
                                    <button type="button" wire:click="decreaseQuantity('{{ $itemKey }}')" aria-label="Decrease quantity">&minus;</button>
                                    <span>{{ $quantity }}</span>
                                    <button type="button" wire:click="increaseQuantity('{{ $itemKey }}')" aria-label="Increase quantity">+</button>
                                </div>
                                <div class="cart-item-pricing">
                                    <span>RM {{ number_format($itemPrice, 2) }}</span>
                                    <strong>RM {{ number_format($itemSubtotal, 2) }}</strong>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <aside class="cart-summary-panel">
                <div class="cart-summary-topline">
                    <span>Order summary</span>
                    <span class="cart-summary-dot"></span>
                </div>
                <div class="cart-summary-lines">
                    <div><span>Merchandise</span><strong>RM {{ number_format($total, 2) }}</strong></div>
                    <div><span>Shipping</span><span class="cart-muted">Calculated next</span></div>
                </div>
                <div class="cart-summary-total">
                    <span>Estimated total</span>
                    <strong>RM {{ number_format($total, 2) }}</strong>
                </div>
                <a class="cart-primary-action cart-checkout-action" href="{{ route('guest.checkout') }}">
                    Continue to delivery
                    <span aria-hidden="true">&rarr;</span>
                </a>
                <p class="cart-summary-note">Secure demo checkout. You will choose a simulated payment result on the next step.</p>
                <div class="cart-trust-row">
                    <span>01</span><span>Small-batch pieces</span>
                </div>
                <div class="cart-trust-row">
                    <span>02</span><span>Shipping calculated by state</span>
                </div>
            </aside>
        </div>

        @if ($products->count() > 0)
            <section class="cart-recommendations">
                <div class="cart-panel-heading">
                    <span>Before you go</span>
                    <a class="cart-text-link" href="{{ route('shop.all') }}">See all <span aria-hidden="true">&rarr;</span></a>
                </div>
                <div class="cart-recommendation-grid">
                    @foreach ($products as $product)
                        <a class="cart-recommendation-card" href="{{ route('product.show', $product->slug) }}">
                            <div class="cart-recommendation-media">
                                <img src="{{ $product->product_image }}" alt="{{ $product->title }}">
                            </div>
                            <div class="cart-recommendation-copy">
                                <span>{{ $product->category->name ?? 'Edition' }}</span>
                                <strong>{{ $product->title }}</strong>
                                <em>RM {{ number_format($product->price, 2) }}</em>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</div>
