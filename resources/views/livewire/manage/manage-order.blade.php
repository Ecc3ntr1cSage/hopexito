@section('title', 'Order History | HopeXito')
<div class="orders-page" x-data="{ section: 'purchases', filter: 'all', query: '', salesQuery: '' }">
    <div class="orders-container">
        <x-jet-session-message />

        <header class="orders-hero">
            <div>
                <p class="orders-kicker">Account / orders</p>
                <h1>Order history</h1>
                <p class="orders-lede">Everything you bought and every product people bought from you, in one place.</p>
            </div>
            <a href="{{ route('search') }}" class="orders-hero-link">Keep browsing <span aria-hidden="true">&nearr;</span></a>
        </header>

        <nav class="orders-view-tabs" role="tablist" aria-label="Order history views">
            <button type="button" role="tab" :class="{ 'is-active': section === 'purchases' }" @click="section = 'purchases'" :aria-selected="section === 'purchases'">
                <span>What you buy</span><small>{{ $stats['orders'] }} {{ Str::plural('order', $stats['orders']) }}</small>
            </button>
            <button type="button" role="tab" :class="{ 'is-active': section === 'sales' }" @click="section = 'sales'" :aria-selected="section === 'sales'">
                <span>What people buy from you</span><small>{{ $salesStats['items'] }} {{ Str::plural('item', $salesStats['items']) }}</small>
            </button>
        </nav>

        <section x-show="section === 'purchases'" x-cloak role="tabpanel" aria-label="Products you bought">
            <section class="orders-stat-rail" aria-label="Purchase summary">
                <div><span>Total orders</span><strong>{{ $stats['orders'] }}</strong></div>
                <div><span>Items purchased</span><strong>{{ $stats['items'] }}</strong></div>
                <div><span>In progress</span><strong>{{ $stats['active'] }}</strong></div>
                <div><span>Total spent</span><strong>RM{{ number_format($stats['spent'], 2) }}</strong></div>
            </section>

            <div class="orders-toolbar">
                <div class="orders-filters" role="group" aria-label="Filter purchases">
                    <button type="button" :class="{ 'is-active': filter === 'all' }" @click="filter = 'all'" :aria-pressed="filter === 'all'">All orders <span>{{ $stats['orders'] }}</span></button>
                    <button type="button" :class="{ 'is-active': filter === 'active' }" @click="filter = 'active'" :aria-pressed="filter === 'active'">In progress <span>{{ $stats['active'] }}</span></button>
                    <button type="button" :class="{ 'is-active': filter === 'delivered' }" @click="filter = 'delivered'" :aria-pressed="filter === 'delivered'">Delivered <span>{{ $stats['orders'] - $stats['active'] }}</span></button>
                </div>
                <label class="orders-search">
                    <span class="sr-only">Search purchases</span>
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
                                                <div>
                                                    <p class="order-item-category">{{ $item->product?->category ?? 'Product' }}</p>
                                                    <h2>{{ $item->title }}</h2>
                                                </div>
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
                        <h2>No purchases yet</h2>
                        <p>Your next favourite piece is waiting to be discovered.</p>
                        <a href="{{ route('search') }}" class="orders-empty-action">Explore the shop <span aria-hidden="true">&nearr;</span></a>
                    </div>
                @endforelse
                <div class="orders-filter-empty" x-show="{{ $orders->count() }} > 0 && !Array.from($root.querySelectorAll('.order-card')).some(card => card.style.display !== 'none')" x-cloak>
                    <h2>No matching orders</h2>
                    <p>Try a different search or filter.</p>
                </div>
            </div>
        </section>

        <section class="sales-panel" x-show="section === 'sales'" x-cloak role="tabpanel" aria-label="Products people bought from you">
            <section class="orders-stat-rail" aria-label="Sales summary">
                <div><span>Orders with your products</span><strong>{{ $salesStats['orders'] }}</strong></div>
                <div><span>Items sold</span><strong>{{ $salesStats['items'] }}</strong></div>
                <div><span>Gross sales</span><strong>RM{{ number_format($salesStats['gross'], 2) }}</strong></div>
                <div><span>Your earnings</span><strong>RM{{ number_format($salesStats['earnings'], 2) }}</strong></div>
            </section>

            <div class="orders-toolbar sales-toolbar">
                <div>
                    <p class="sales-heading">Products people bought from you</p>
                    <p class="sales-caption">Product, buyer, variant, and payout details for every sale.</p>
                </div>
                <label class="orders-search">
                    <span class="sr-only">Search sales</span>
                    <span aria-hidden="true">/</span>
                    <input type="search" x-model="salesQuery" placeholder="Search products or buyers" />
                </label>
            </div>

            @auth
                <div class="sales-list">
                    @forelse ($sales as $sale)
                        @php
                            $saleStatus = match ((int) ($sale->order?->status ?? 0)) {
                                1 => 'Order placed',
                                2 => 'Processing',
                                3 => 'On the way',
                                4 => 'Delivered',
                                default => 'Order received',
                            };
                            $saleSearchText = strtolower(implode(' ', array_filter([$sale->title, $sale->order?->name, $sale->billplz_id])));
                            $saleDate = $sale->order?->paid_at ?: $sale->created_at;
                            $saleLineTotal = (float) $sale->price * (int) $sale->quantity;
                            $saleEarnings = $saleLineTotal * (float) ($sale->product?->commission_rate ?? 0.15);
                        @endphp

                        <article class="sale-card" data-sales-search="{{ $saleSearchText }}" x-show="!salesQuery || $el.dataset.salesSearch.includes(salesQuery.toLowerCase())" x-cloak>
                            <div class="sale-media">
                                @if ($sale->product?->product_card_image)
                                    <img src="{{ $sale->product->product_card_image }}" alt="{{ $sale->title }}" />
                                @else
                                    <span aria-hidden="true">HX</span>
                                @endif
                            </div>
                            <div class="sale-content">
                                <div class="sale-header">
                                    <div>
                                        <p class="sale-kicker">Sold product</p>
                                        <h2>{{ $sale->title }}</h2>
                                        <p class="sale-product-detail">{{ $sale->product?->category ?? 'Product' }} / {{ $sale->size }} / {{ $sale->color }}</p>
                                    </div>
                                    <strong>RM{{ number_format($saleLineTotal, 2) }}</strong>
                                </div>
                                <div class="sale-detail-grid">
                                    <div><span>Purchased by</span><strong>{{ Str::replaceLast('(G)', '', $sale->order?->name ?? 'Guest') }}</strong></div>
                                    <div><span>Quantity</span><strong>{{ $sale->quantity }}</strong></div>
                                    <div><span>Unit price</span><strong>RM{{ number_format($sale->price, 2) }}</strong></div>
                                    <div><span>Your earnings</span><strong class="sale-earnings">RM{{ number_format($saleEarnings, 2) }}</strong></div>
                                </div>
                                <div class="sale-footer">
                                    <span>Order #{{ strtoupper(substr($sale->billplz_id, 0, 8)) }} / {{ \Carbon\Carbon::parse($saleDate)->format('M d, Y') }}</span>
                                    <span class="sale-status">{{ $saleStatus }}</span>
                                    @if ($sale->product)
                                        <a href="{{ route('product.show', $sale->product->slug) }}">View product <span aria-hidden="true">&nearr;</span></a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="orders-empty">
                            <span class="orders-empty-mark" aria-hidden="true">/</span>
                            <h2>No sales yet</h2>
                            <p>When someone buys one of your products, the full order detail will appear here.</p>
                            <a href="{{ route('product.manage') }}" class="orders-empty-action">Manage your products <span aria-hidden="true">&nearr;</span></a>
                        </div>
                    @endforelse
                    <div class="orders-filter-empty" x-show="{{ $sales->count() }} > 0 && !Array.from($root.querySelectorAll('.sale-card')).some(card => card.style.display !== 'none')" x-cloak>
                        <h2>No matching sales</h2>
                        <p>Try a different product or buyer.</p>
                    </div>
                </div>
            @else
                <div class="orders-empty">
                    <span class="orders-empty-mark" aria-hidden="true">/</span>
                    <h2>Sign in to see your sales</h2>
                    <p>Your creator sales and earnings are available from your account.</p>
                    <a href="{{ route('home', ['auth' => 'login']) }}" class="orders-empty-action" @click.prevent="$dispatch('open-auth')">Log in <span aria-hidden="true">&nearr;</span></a>
                </div>
            @endauth
        </section>
    </div>
</div>
