@section('title', 'Payment | HopeXito')

<x-app-layout>
    <x-jet-session-message />

    @php
        $paymentMethods = [
            ['id' => 'demo-card', 'mark' => 'CARD', 'label' => 'Demo card', 'caption' => 'Instant simulation'],
            ['id' => 'online-banking', 'mark' => 'FPX', 'label' => 'Online banking', 'caption' => 'FPX-style simulation'],
            ['id' => 'xito-wallet', 'mark' => 'HX', 'label' => 'Xito wallet', 'caption' => 'Stored balance simulation'],
        ];
        $isAuthenticated = Auth::check();
    @endphp

    <div class="payment-page" x-data="{ method: 'demo-card', result: 'success' }">
        <header class="payment-hero">
            <div>
                <div class="payment-kicker">HopeXito / payment</div>
                <h1>Make the final call.</h1>
                <p>This is a simulation environment. Nothing is charged, and you control the outcome.</p>
            </div>
            <a class="payment-close-link" href="{{ route('guest.checkout') }}" aria-label="Return to delivery">&times;</a>
        </header>

        <nav class="checkout-progress payment-progress" aria-label="Checkout progress">
            <span class="is-complete"><b>01</b> Bag</span>
            <i></i>
            <span class="is-complete"><b>02</b> Delivery</span>
            <i></i>
            <span class="is-active"><b>03</b> Payment</span>
        </nav>

        <div class="payment-layout">
            <section class="payment-form-panel">
                <div class="payment-panel-heading">
                    <div class="payment-kicker">Payment method</div>
                    <h2>How should we simulate it?</h2>
                </div>

                <form method="POST" action="{{ route('billplz-store') }}">
                    @csrf
                    <div class="payment-method-grid">
                        @foreach ($paymentMethods as $paymentMethod)
                            <label class="payment-method-card" :class="method === '{{ $paymentMethod['id'] }}' ? 'is-selected' : ''">
                                <input type="radio" name="payment_method" value="{{ $paymentMethod['id'] }}" x-model="method">
                                <span class="payment-method-mark">{{ $paymentMethod['mark'] }}</span>
                                <span class="payment-method-copy">
                                    <strong>{{ $paymentMethod['label'] }}</strong>
                                    <small>{{ $paymentMethod['caption'] }}</small>
                                </span>
                                <span class="payment-method-check">&check;</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="payment-outcome-block">
                        <div class="payment-outcome-heading">
                            <div>
                                <div class="payment-kicker">Test outcome</div>
                                <h3>Choose what happens next.</h3>
                            </div>
                            <span class="payment-simulation-tag">DEMO</span>
                        </div>
                        <div class="payment-outcome-grid">
                            <label class="payment-outcome-card" :class="result === 'success' ? 'is-success' : ''">
                                <input type="radio" name="payment_result" value="success" x-model="result">
                                <span class="payment-outcome-indicator"></span>
                                <span><strong>Simulate success</strong><small>Place the order and open your receipt.</small></span>
                            </label>
                            <label class="payment-outcome-card" :class="result === 'failed' ? 'is-failed' : ''">
                                <input type="radio" name="payment_result" value="failed" x-model="result">
                                <span class="payment-outcome-indicator"></span>
                                <span><strong>Simulate failure</strong><small>Return to delivery without creating an order.</small></span>
                            </label>
                        </div>
                    </div>

                    <div class="payment-form-footer">
                        <a class="payment-back-link" href="{{ route('guest.checkout') }}">&larr; Back to delivery</a>
                        <button class="payment-primary-action" type="submit">
                            <span x-text="result === 'success' ? 'Complete payment' : 'Try payment again'"></span>
                            <b>&rarr;</b>
                        </button>
                    </div>
                    <p class="payment-terms">Demo only, no real payment is processed.</p>
                </form>
            </section>

            <aside class="payment-summary-panel">
                <div class="payment-summary-label">Your order</div>
                <div class="payment-summary-items">
                    @foreach ($cart as $item)
                        @php
                            $productName = $isAuthenticated ? $item->title : $item['name'];
                            $productImage = $isAuthenticated ? $item->display_image : $item['options']['product_image'];
                            $productSize = $isAuthenticated ? $item->size : $item['options']['size'];
                            $productColor = $isAuthenticated ? $item->color : $item['options']['color'];
                            $quantity = $isAuthenticated ? $item->quantity : $item['qty'];
                            $itemSubtotal = $isAuthenticated ? $item->subtotal : $item['price'] * $item['qty'];
                        @endphp
                        <div class="payment-summary-item">
                            <img src="{{ $productImage }}" alt="{{ $productName }}">
                            <div>
                                <strong>{{ $productName }}</strong>
                                <span>{{ $productSize }} / {{ $productColor }} &middot; {{ $quantity }}x</span>
                            </div>
                            <b>RM {{ number_format($itemSubtotal, 2) }}</b>
                        </div>
                    @endforeach
                </div>
                <div class="payment-summary-rule"></div>
                <div class="payment-summary-line"><span>Subtotal</span><strong>RM {{ number_format($subtotal, 2) }}</strong></div>
                <div class="payment-summary-line"><span>JNT Express</span><strong>RM {{ number_format($delivery, 2) }}</strong></div>
                <div class="payment-summary-total"><span>Total</span><strong>RM {{ number_format($total, 2) }}</strong></div>
                <div class="payment-address-block">
                    <span>Delivering to</span>
                    @if ($isAuthenticated)
                        <strong>{{ Auth::user()->name }}</strong>
                        <p>{{ Auth::user()->address }}, {{ Auth::user()->postcode }}<br>{{ Auth::user()->state }}</p>
                    @else
                        <strong>{{ $details['name'] }}</strong>
                        <p>{{ $details['address'] }}, {{ $details['postcode'] }}<br>{{ $state }}</p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
