@section('title', 'Order History | HopeXito')
<div class="orders-page" x-data="{ filter: 'all', query: '' }">
    <div class="orders-container">
        <x-jet-session-message />

        <header class="orders-hero">
            <div>
                <p class="orders-kicker">Account / orders</p>
                <h1>Order history</h1>
                <p class="orders-lede">A clear view of what you bought, where it is, and what to do next.</p>
            </div>
            <a href="{{ route('search') }}" class="orders-hero-link">Keep browsing <span aria-hidden="true">&nearr;</span></a>
        </header>

        <section class="orders-stat-rail" aria-label="Order summary">
            <div><span>Total orders</span><strong>{{ $stats['orders'] }}</strong></div>
            <div><span>Items purchased</span><strong>{{ $stats['items'] }}</strong></div>
            <div><span>In progress</span><strong>{{ $stats['active'] }}</strong></div>
            <div><span>Total spent</span><strong>RM{{ number_format($stats['spent'], 2) }}</strong></div>
        </section>

        <div class="orders-toolbar">
            <div class="orders-filters" role="group" aria-label="Filter orders">
                <button type="button" :class="{ 'is-active': filter === 'all' }" @click="filter = 'all'" :aria-pressed="filter === 'all'">All orders <span>{{ $stats['orders'] }}</span></button>
                <button type="button" :class="{ 'is-active': filter === 'active' }" @click="filter = 'active'" :aria-pressed="filter === 'active'">In progress <span>{{ $stats['active'] }}</span></button>
                <button type="button" :class="{ 'is-active': filter === 'delivered' }" @click="filter = 'delivered'" :aria-pressed="filter === 'delivered'">Delivered <span>{{ $stats['orders'] - $stats['active'] }}</span></button>
            </div>
            <label class="orders-search">
                <span class="sr-only">Search orders</span>
                <span aria-hidden="true">/</span>
                <input type="search" x-model="query" placeholder="Search by order or product" />
            </label>
        </div>

        <div class="orders-list">
            @forelse ($orders as $order)
                @php
                    $statusKey = $order->status == 4 ? 'delivered' : 'active';
                    $statusLabel = match ((int) $order->status) {
                        1 => 'Order placed',
                        2 => 'Processing',
                        3 => 'On the way',
                        4 => 'Delivered',
                        default => 'Order received',
                    };
                    $statusDescription = match ((int) $order->status) {
                        1 => 'We have your order and will start preparing it soon.',
                        2 => 'Your order is being prepared by the creator.',
                        3 => 'Your order is with the delivery partner.',
                        4 => 'This order has arrived. We hope you love it.',
                        default => 'We are checking the latest update for this order.',
                    };
                    $searchText = strtolower($order->id . ' ' . $order->productOrder->pluck('title')->implode(' '));
                    $currentStep = min(max((int) $order->status, 1), 4);
                    $placedAt = $order->paid_at ?: $order->created_at;
                @endphp

                <article class="order-card" data-order-filter="{{ $statusKey }}" data-order-search="{{ $searchText }}" x-show="(filter === 'all' || $el.dataset.orderFilter === filter) && (!query || $el.dataset.orderSearch.includes(query.toLowerCase()))" x-cloak>
                    <div class="order-card-header">
                        <div>
                            <p class="order-card-label">Order #{{ strtoupper(substr($order->id, 0, 8)) }}</p>
                            <p class="order-card-date">Placed {{ \Carbon\Carbon::parse($placedAt)->format('M d, Y') }}</p>
                        </div>
                        <div class="order-card-total">
                            <span>{{ $statusLabel }}</span>
                            <strong>RM{{ number_format($order->amount, 2) }}</strong>
                        </div>
                    </div>

                    <div class="order-card-main">
                        <div class="order-items">
                            @foreach ($order->productOrder as $item)
                                <div class="order-item">
                                    <div class="order-item-media">
                                        @if ($item->product?->product_card_image)
                                            <img src="{{ $item->product->product_card_image }}" alt="{{ $item->title }}" />
                                        @else
                                            <span aria-hidden="true">HX</span>
                                        @endif
                                    </div>
                                    <div class="order-item-copy">
                                        <div class="order-item-topline">
                                            <h2>{{ $item->title }}</h2>
                                            <strong>RM{{ number_format($item->price * $item->quantity, 2) }}</strong>
                                        </div>
                                        <p>{{ $item->size }} / {{ $item->color }} <span>Quantity {{ $item->quantity }}</span></p>
                                        @if ($item->product)
                                            <a href="{{ route('product.show', $item->product->slug) }}" class="order-buy-again">Buy again <span aria-hidden="true">&nearr;</span></a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <aside class="order-card-aside">
                            <p class="order-aside-label">Delivery details</p>
                            <p class="order-address">{{ $order->address }}<br>{{ $order->postcode }}, {{ $order->state }}</p>
                            <div class="order-status-note">
                                <span class="order-status-mark" aria-hidden="true"></span>
                                <div><strong>{{ $statusLabel }}</strong><p>{{ $statusDescription }}</p></div>
                            </div>
                            @if ($order->status == 3 && $order->tracking_number)
                                <div class="order-actions">
                                    <button type="button" wire:click="received('{{ $order->id }}')" class="order-action-primary">Mark received</button>
                                    <a href="https://www.jtexpress.my/tracking/{{ $order->tracking_number }}" target="_blank" rel="noreferrer" class="order-action-secondary">Track package <span aria-hidden="true">&nearr;</span></a>
                                </div>
                            @elseif ($order->status == 4)
                                <p class="order-complete">Completed</p>
                            @endif
                        </aside>
                    </div>

                    <div class="order-progress" aria-label="{{ $statusLabel }}">
                        @foreach (['Placed', 'Processing', 'Shipped', 'Delivered'] as $stepIndex => $step)
                            <div class="order-step {{ $stepIndex + 1 <= $currentStep ? 'is-complete' : '' }} {{ $stepIndex + 1 === $currentStep ? 'is-current' : '' }}">
                                <span aria-hidden="true"></span><small>{{ $step }}</small>
                            </div>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="orders-empty">
                    <span class="orders-empty-mark" aria-hidden="true">/</span>
                    <h2>No orders yet</h2>
                    <p>Your next favourite piece is waiting to be discovered.</p>
                    <a href="{{ route('search') }}" class="orders-empty-action">Explore the shop <span aria-hidden="true">&nearr;</span></a>
                </div>
            @endforelse
            <div class="orders-filter-empty" x-show="{{ $orders->count() }} > 0 && !Array.from($root.querySelectorAll('.order-card')).some(card => card.style.display !== 'none')" x-cloak>
                <h2>No matching orders</h2>
                <p>Try a different search or filter.</p>
            </div>
        </div>
    </div>
</div>
