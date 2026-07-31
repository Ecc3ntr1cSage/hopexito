@section('title', $user->name . ' | HopeXito')

@php
    $isOwner = Auth::check() && Auth::id() === $user->id;
    $profile = $user->profile;
    $socialLinks = collect([
        ['label' => 'Instagram', 'value' => $profile?->instagram],
        ['label' => 'Behance', 'value' => $profile?->behance],
        ['label' => 'Website', 'value' => $profile?->website],
        ['label' => 'TikTok', 'value' => $profile?->tiktok],
    ])->filter(fn ($link) => filled($link['value']))->map(function ($link) {
        $value = trim($link['value']);
        $link['href'] = str_starts_with($value, 'http') ? $value : 'https://'.$value;
        return $link;
    });
@endphp

<x-app-layout>
    <main class="profile-page">
        <section class="profile-hero profile-container">
            <div class="profile-cover-shell profile-reveal">
                <div class="profile-cover-core">
                    @if ($profile?->cover_image)
                        <img src="{{ asset('storage/cover-image/' . $profile->cover_image) }}" alt="{{ $user->name }} cover image">
                        <span class="profile-cover-wash" aria-hidden="true"></span>
                    @else
                        <div class="profile-cover-placeholder" aria-hidden="true">
                            <div class="profile-cover-grid"></div>
                            <span class="profile-cover-mark">HX</span>
                            <span class="profile-cover-caption">Independent work<br>since 2026</span>
                        </div>
                    @endif
                    <span class="profile-cover-index">Creator archive / {{ str_pad((string) $user->id, 3, '0', STR_PAD_LEFT) }}</span>
                    <span class="profile-cover-name">{{ $user->name }}</span>
                </div>
            </div>

            <div class="profile-identity-layout">
                <div class="profile-identity profile-reveal profile-reveal-delay">
                    <div class="profile-avatar-shell">
                        <div class="profile-avatar-core"><img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"></div>
                    </div>
                    <div class="profile-identity-copy">
                        <span class="profile-eyebrow"><i></i> Creator profile</span>
                        <h1>{{ $user->name }}</h1>
                        <p>{{ $profile?->bio ?: 'A maker building things worth keeping.' }}</p>
                        <div class="profile-links">
                            <a href="{{ route('explore') }}">Marketplace <span aria-hidden="true">&nearr;</span></a>
                            @foreach ($socialLinks as $social)
                                <a href="{{ $social['href'] }}" target="_blank" rel="noreferrer">{{ $social['label'] }} <span aria-hidden="true">&nearr;</span></a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="profile-identity-side profile-reveal profile-reveal-delay-more">
                    @if ($isOwner)
                        <div class="profile-workspace">
                            <span class="profile-workspace-label">Your workspace</span>
                            <div class="profile-workspace-links">
                                <a href="{{ route('profile.show') }}" class="profile-workspace-link profile-workspace-link-primary">
                                    <span><small>01</small> Profile settings</span><b aria-hidden="true">&nearr;</b>
                                </a>
                                <a href="{{ route('product.manage') }}" class="profile-workspace-link">
                                    <span><small>02</small> Manage products</span><b aria-hidden="true">&nearr;</b>
                                </a>
                                <a href="{{ route('product.create') }}" class="profile-workspace-link">
                                    <span><small>03</small> Create a product</span><b aria-hidden="true">&nearr;</b>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="profile-visitor-note">
                            <span class="profile-workspace-label">Open studio</span>
                            <p>Independent ideas, made visible and ready to wear.</p>
                            <a href="{{ route('explore') }}" class="profile-inline-link">Explore the marketplace <span aria-hidden="true">&nearr;</span></a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="profile-stat-strip profile-reveal profile-reveal-delay-more">
                <div><strong>{{ $productsCount }}</strong><span>{{ $isOwner ? 'total designs' : 'public designs' }}</span></div>
                <div><strong>{{ $totalSold }}</strong><span>pieces moved</span></div>
                <div><strong>{{ $user->created_at?->format('Y') ?? '2026' }}</strong><span>maker since</span></div>
                <span class="profile-stat-note">Made by people<br>with something to say.</span>
            </div>
        </section>

        <section class="profile-work-section profile-section">
            <div class="profile-section-heading profile-container">
                <div>
                    <span class="profile-eyebrow"><i></i> 01 / Selected work</span>
                    <h2>Objects with a point of view.</h2>
                    <p>{{ $isOwner ? 'Your public and private editions, arranged for a quick studio read.' : 'A small run of pieces made by '.$user->name.'.' }}</p>
                </div>
                <span class="profile-section-index">{{ str_pad((string) $productsCount, 2, '0', STR_PAD_LEFT) }} editions</span>
            </div>

            <div class="profile-content-grid profile-container">
                <div class="profile-products-grid">
                    @forelse ($products as $product)
                        <a href="{{ route('product.show', $product->slug) }}" class="profile-product-card {{ $loop->first ? 'profile-product-featured' : '' }}" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <div class="profile-product-frame">
                                <div class="profile-product-core">
                                    <img src="{{ $product->product_image }}" alt="{{ $product->title }}" :class="hover ? 'profile-product-hidden' : ''">
                                    @if ($product->product_image_2)
                                        <img src="{{ $product->product_image_2 }}" alt="" class="profile-product-alt" :class="hover ? 'profile-product-visible' : ''">
                                    @endif
                                    <span class="profile-product-arrow" aria-hidden="true">&nearr;</span>
                                    @if ($product->visibility === 'private')
                                        <span class="profile-private-label">Private</span>
                                    @endif
                                </div>
                            </div>
                            <div class="profile-product-meta">
                                <div><span>{{ $product->category }}</span><h3>{{ $product->title }}</h3></div>
                                <strong>RM{{ number_format($product->price, 2) }}</strong>
                            </div>
                        </a>
                    @empty
                        <div class="profile-empty">
                            <span class="profile-eyebrow"><i></i> Nothing published yet</span>
                            <p>{{ $isOwner ? 'Your first piece is waiting in the product studio.' : 'This creator is still preparing their first edition.' }}</p>
                            @if ($isOwner)
                                <a href="{{ route('product.create') }}" class="profile-inline-link">Open product studio <span aria-hidden="true">&nearr;</span></a>
                            @endif
                        </div>
                    @endforelse
                </div>

                <aside class="profile-note-shell">
                    <div class="profile-note-core">
                        <span class="profile-eyebrow"><i></i> Field notes</span>
                        <p class="profile-note-quote">“The best pieces carry a little of the person who made them.”</p>
                        <div class="profile-note-rule"></div>
                        <p class="profile-note-copy">Every HopeXito edition is made to order, so the work stays close to the person behind it.</p>
                        <a href="{{ route('shop.all') }}" class="profile-inline-link">Browse all makers <span aria-hidden="true">&nearr;</span></a>
                    </div>
                </aside>
            </div>

            <div class="profile-pagination profile-container">{{ $products->links('/vendor/pagination/tailwind') }}</div>
        </section>
    </main>
</x-app-layout>
