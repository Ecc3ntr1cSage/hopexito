@section('title', 'Manage Products | HopeXito')

<div class="manage-page" x-data="{ nav: 'products', collectionModal: false }">
    <x-jet-session-message />

    <section class="manage-hero manage-container">
        <div class="manage-breadcrumb"><a href="{{ route('people', Auth::user()->name) }}">Your profile</a><span>/</span><span>Workspace</span></div>
        <div class="manage-hero-grid">
            <div class="manage-hero-copy manage-reveal">
                <span class="manage-eyebrow"><i></i> Product studio / 02</span>
                <h1>Work, in one place.</h1>
                <p>Keep your editions moving, your shelves considered, and your public presence in sync.</p>
            </div>
            <div class="manage-hero-actions manage-reveal manage-reveal-delay">
                <a href="{{ route('product.create') }}" class="manage-primary-action group"><span>Create a product</span><b aria-hidden="true">&nearr;</b></a>
                <a href="{{ route('people', Auth::user()->name) }}" class="manage-secondary-action">View public profile <span aria-hidden="true">&nearr;</span></a>
            </div>
        </div>
        <div class="manage-stat-rail manage-reveal manage-reveal-delay-more">
            <div><strong>{{ $products->count() }}</strong><span>active editions</span></div>
            <div><strong>{{ $archives->count() }}</strong><span>in archive</span></div>
            <div><strong>{{ $productCollections->count() }}</strong><span>collections</span></div>
            <div class="manage-stat-note">A quiet place<br>to make things real.</div>
        </div>
    </section>

    <section class="manage-workspace manage-container">
        <nav class="manage-tabs" aria-label="Product workspace sections">
            <button type="button" :class="nav === 'products' ? 'is-active' : ''" @click="nav = 'products'"><span>01</span> Products <b>{{ $products->count() }}</b></button>
            <button type="button" :class="nav === 'collections' ? 'is-active' : ''" @click="nav = 'collections'"><span>02</span> Collections <b>{{ $productCollections->count() }}</b></button>
            <button type="button" :class="nav === 'archives' ? 'is-active' : ''" @click="nav = 'archives'"><span>03</span> Archives <b>{{ $archives->count() }}</b></button>
        </nav>

        <section x-cloak x-show="nav === 'products'" x-transition:enter="manage-panel-enter" x-transition:enter-start="manage-panel-start" x-transition:enter-end="manage-panel-end" class="manage-panel">
            <div class="manage-panel-heading">
                <div><span class="manage-eyebrow"><i></i> Live catalogue</span><h2>Your editions</h2></div>
                <label class="manage-search"><span aria-hidden="true">/</span><input type="search" wire:model.lazy="search" placeholder="Search products" aria-label="Search products"></label>
            </div>

            <div class="manage-product-grid">
                @forelse ($products as $product)
                    <article class="manage-product-card" x-data="{ side: '{{ $product->preview == 1 && $product->product_image_2 ? 'back' : 'front' }}', menuOpen: false, editOpen: false }">
                        <div class="manage-product-frame">
                            <div class="manage-product-media">
                                <img x-bind:src="side === 'back' ? @js($product->product_image_2) : @js($product->product_image)" alt="{{ $product->title }}" class="manage-product-image">
                                <div class="manage-product-media-meta"><span>{{ $product->category }}</span><span>{{ str_pad((string) $product->id, 3, '0', STR_PAD_LEFT) }}</span></div>
                                <div class="manage-product-media-status">
                                    @if ($product->status === 3)<span class="manage-status manage-status-pinned">Pinned</span>@endif
                                    <span class="manage-status {{ $product->visibility === 'public' ? 'manage-status-public' : 'manage-status-private' }}">{{ ucfirst($product->visibility) }}</span>
                                </div>
                                <button type="button" class="manage-product-menu-button" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen.toString()" aria-label="Open product actions"><span></span><span></span><span></span></button>
                                <div x-cloak x-show="menuOpen" x-transition:enter="manage-menu-enter" x-transition:enter-start="manage-menu-start" x-transition:enter-end="manage-menu-end" @click.outside="menuOpen = false" class="manage-product-menu">
                                    @if ($product->status === 3)
                                        <button type="button" wire:click="unpinProduct('{{ $product->id }}')" @click="menuOpen = false">Unpin product</button>
                                    @else
                                        <button type="button" wire:click="pinProduct('{{ $product->id }}')" @click="menuOpen = false">Pin to profile</button>
                                    @endif
                                    @if ($product->product_image_2)
                                        <button type="button" @click="side = side === 'front' ? 'back' : 'front'; menuOpen = false" x-text="side === 'front' ? 'Preview back' : 'Preview front'"></button>
                                    @endif
                                    <button type="button" wire:click="forceFill('{{ $product->id }}')" @click="menuOpen = false; editOpen = true">Edit details</button>
                                    @if ($product->visibility === 'public')
                                        <button type="button" wire:click="setVisibility('{{ $product->id }}', 'private')" @click="menuOpen = false">Make private</button>
                                    @else
                                        <button type="button" wire:click="setVisibility('{{ $product->id }}', 'public')" @click="menuOpen = false">Make public</button>
                                    @endif
                                    <button type="button" wire:click="archiveProduct('{{ $product->id }}')" @click="menuOpen = false">Archive</button>
                                    @if ($product->sold == 0 && $inCart[$product->id] == false)
                                        <button type="button" wire:click="deleteProduct('{{ $product->id }}')" @click="menuOpen = false" class="manage-menu-danger">Delete product</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="manage-product-details">
                            <div><span class="manage-product-type">{{ $product->category }} / {{ $product->visibility }}</span><h3>{{ $product->title }}</h3></div>
                            <strong>RM{{ number_format($product->price, 2) }}</strong>
                        </div>
                        <div class="manage-product-metrics"><span><b>{{ $product->sold }}</b> sold</span><span>RM{{ number_format($product->commission, 2) }} commission</span><span>{{ $product->tags }}</span></div>

                        <div x-cloak x-show="editOpen" x-transition.opacity class="manage-modal-backdrop" @click.self="editOpen = false" @keydown.escape.window="editOpen = false">
                            <div class="manage-modal" role="dialog" aria-modal="true" aria-labelledby="edit-product-{{ $product->id }}">
                                <div class="manage-modal-heading"><div><span class="manage-eyebrow"><i></i> Edit edition</span><h2 id="edit-product-{{ $product->id }}">Product details</h2></div><button type="button" class="manage-modal-close" @click="editOpen = false" aria-label="Close edit product dialog">&times;</button></div>
                                <div class="manage-form-field"><label for="title-{{ $product->id }}">Title</label><input id="title-{{ $product->id }}" type="text" wire:model.defer="title"></div>
                                @error('title') <p class="manage-form-error">{{ $message }}</p> @enderror
                                <div class="manage-form-field"><label for="tags-{{ $product->id }}">Tags</label><input id="tags-{{ $product->id }}" type="text" wire:model.defer="tags"></div>
                                @error('tags') <p class="manage-form-error">{{ $message }}</p> @enderror
                                <p class="manage-modal-note">Price and commission are fixed by product type.</p>
                                <button type="button" class="manage-modal-submit" wire:click="editProduct('{{ $product->id }}')" @click="editOpen = false">Save changes <span aria-hidden="true">&nearr;</span></button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="manage-empty"><span class="manage-eyebrow"><i></i> No editions found</span><p>Start with one piece and build your catalogue from there.</p><a href="{{ route('product.create') }}" class="manage-secondary-action">Open product studio <span aria-hidden="true">&nearr;</span></a></div>
                @endforelse
            </div>
        </section>

        <section x-cloak x-show="nav === 'collections'" x-transition:enter="manage-panel-enter" x-transition:enter-start="manage-panel-start" x-transition:enter-end="manage-panel-end" class="manage-panel">
            <div class="manage-panel-heading">
                <div><span class="manage-eyebrow"><i></i> Curated shelves</span><h2>Collections</h2></div>
                <button type="button" class="manage-primary-action manage-small-action" @click="collectionModal = true"><span>Add collection</span><b aria-hidden="true">+</b></button>
            </div>

            <div class="manage-collection-grid">
                @forelse ($productCollections as $collection)
                    <article class="manage-collection-card">
                        <div class="manage-collection-cover" style="background-image: url('{{ asset('storage/collection-image/' . $collection->collection_image) }}')">
                            <div class="manage-collection-cover-shade"></div>
                            <div class="manage-collection-cover-meta"><span>{{ $collection->product->count() }} editions</span><button type="button" wire:click="deleteCollection('{{ $collection->id }}')" aria-label="Delete {{ $collection->title }}">&times;</button></div>
                            <h3>{{ $collection->title }}</h3>
                        </div>
                        <div class="manage-collection-products">
                            <span class="manage-collection-label">Assign editions</span>
                            <div class="manage-collection-product-list">
                                @foreach ($products as $product)
                                    @if ((string) $product->collection_id === (string) $collection->id)
                                        <button type="button" wire:click="removeFromCollection('{{ $product->id }}','{{ $collection->id }}')" class="is-assigned">{{ $product->title }} <span aria-hidden="true">&minus;</span></button>
                                    @elseif ($product->collection_id === null)
                                        <button type="button" wire:click="addToCollection('{{ $product->id }}','{{ $collection->id }}')">{{ $product->title }} <span aria-hidden="true">+</span></button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="manage-empty"><span class="manage-eyebrow"><i></i> No collections yet</span><p>Group related pieces into a shelf your audience can browse.</p><button type="button" class="manage-secondary-action" @click="collectionModal = true">Create your first collection <span aria-hidden="true">&nearr;</span></button></div>
                @endforelse
            </div>
        </section>

        <section x-cloak x-show="nav === 'archives'" x-transition:enter="manage-panel-enter" x-transition:enter-start="manage-panel-start" x-transition:enter-end="manage-panel-end" class="manage-panel">
            <div class="manage-panel-heading"><div><span class="manage-eyebrow"><i></i> Quiet shelf</span><h2>Archives</h2></div><span class="manage-panel-note">Archived pieces are hidden from your public profile.</span></div>
            @if ($noArchives)
                <div class="manage-empty"><span class="manage-eyebrow"><i></i> Archive is clear</span><p>When a piece has had its run, it will live here until you are ready to bring it back.</p></div>
            @else
                <div class="manage-archive-grid">
                    @foreach ($archives as $archive)
                        <article class="manage-archive-card">
                            <div class="manage-archive-frame"><div class="manage-archive-media"><img src="{{ $archive->product_image }}" alt="{{ $archive->title }}"><span>Archived / {{ str_pad((string) $archive->id, 3, '0', STR_PAD_LEFT) }}</span></div></div>
                            <div class="manage-product-details"><div><span class="manage-product-type">{{ $archive->category }}</span><h3>{{ $archive->title }}</h3></div><strong>RM{{ number_format($archive->price, 2) }}</strong></div>
                            <div class="manage-product-metrics"><span><b>{{ $archive->sold }}</b> sold</span><span>RM{{ number_format($archive->commission, 2) }} commission</span></div>
                            <button type="button" class="manage-restore-button" wire:click="unarchiveProduct('{{ $archive->id }}')">Restore to catalogue <span aria-hidden="true">&nearr;</span></button>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </section>

    <div x-cloak x-show="collectionModal" x-transition.opacity class="manage-modal-backdrop" @click.self="collectionModal = false" @keydown.escape.window="collectionModal = false">
        <div class="manage-modal" role="dialog" aria-modal="true" aria-labelledby="new-collection-title">
            <div class="manage-modal-heading"><div><span class="manage-eyebrow"><i></i> New shelf</span><h2 id="new-collection-title">Create collection</h2></div><button type="button" class="manage-modal-close" @click="collectionModal = false" aria-label="Close collection dialog">&times;</button></div>
            <form method="POST" action="{{ route('upload.collection') }}" enctype="multipart/form-data" class="manage-collection-form">
                @csrf
                <div class="manage-form-field"><label for="collection-image">Cover image</label><input type="file" id="collection-image" name="collection_image" wire:model.defer="collection_image"></div>
                <div class="manage-form-field"><label for="collection-title">Title</label><input id="collection-title" type="text" name="title" required></div>
                <button type="submit" class="manage-modal-submit">Save collection <span aria-hidden="true">&nearr;</span></button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script>
        if (window.FilePond) {
            FilePond.registerPlugin(FilePondPluginImagePreview);
            const collectionInput = document.querySelector('#collection-image');
            if (collectionInput) {
                FilePond.create(collectionInput);
                FilePond.setOptions({ server: { url: '{{ route('upload') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } } });
            }
        }
    </script>
</div>
