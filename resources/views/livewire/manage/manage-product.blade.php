@section('title', 'Manage Products | HopeXito')
<div class="max-w-3xl min-h-screen mx-auto mt-8 mb-32 text-white" x-data="{ nav: 1 }">
    <x-jet-session-message />
    <h1
        class="px-2 text-2xl font-bold text-transparent md:px-0 bg-clip-text bg-gradient-to-r from-red-400 to-indigo-800">
        Product Management</h1>
    <p class="px-2 my-2 mb-8 md:px-0">Take control of your product listings with our comprehensive product management
        tool.
    </p>
    <ul class="flex mb-10 border-b border-gray-100">
        <li class="flex-1 transition hover:bg-white/10">
            <a class="relative block p-4 cursor-pointer" x-on:click="nav = 1">
                <span x-bind:class="nav == 1 ? 'bg-fuchsia-600' : 'bg-tranparent'"
                    class="absolute inset-x-0 w-full h-px -bottom-px"></span>
                <div class="flex items-center justify-center gap-4">
                    <span x-bind:class="nav == 1 ? 'text-fuchsia-500' : 'text-white'"
                        class="text-sm font-medium">Products</span>
                </div>
            </a>
        </li>
        <li class="flex-1 transition hover:bg-white/10">
            <a class="relative block p-4 cursor-pointer" x-on:click="nav = 2">
                <span x-bind:class="nav == 2 ? 'bg-fuchsia-600' : 'bg-tranparent'"
                    class="absolute inset-x-0 w-full h-px -bottom-px"></span>
                <div class="flex items-center justify-center gap-4">
                    <span x-bind:class="nav == 2 ? 'text-fuchsia-500' : 'text-white'"
                        class="text-sm font-medium">Collection</span>
                </div>
            </a>
        </li>
        <li class="flex-1 transition hover:bg-white/10">
            <a class="relative block p-4 cursor-pointer" x-on:click="nav = 3">
                <span x-bind:class="nav == 3 ? 'bg-fuchsia-600' : 'bg-tranparent'"
                    class="absolute inset-x-0 w-full h-px -bottom-px"></span>
                <div class="flex items-center justify-center gap-4">
                    <span x-bind:class="nav == 3 ? 'text-fuchsia-500' : 'text-white'"
                        class="text-sm font-medium">Archives</span>
                </div>
            </a>
        </li>
    </ul>
    <div x-cloak x-show="nav == 1" x-transition.opacity x-transition:enter.duration.500ms
        x-transition:leave.duration.100ms>
        <x-jet-input class="mx-3 mb-4 md:mx-0" type="text" wire:model.lazy="search"
            placeholder="Search by product name" />
        <div class="flex flex-wrap justify-start gap-2 px-2 font-mono md:gap-6 md:px-0">
            @foreach ($products as $product)
                <div class="" x-data="{ open: false, modal: false, dropdown: false, id: '{{ $product->id }}' }" x-on:mouseenter="open = true"
                    x-on:mouseleave="open = false">
                    <div class="relative w-48 md:w-60">
                        <img class="object-cover object-center transition duration-500 shadow-md shadow-purple-500/30 rounded-xl"
                            src="{{ $product->product_image }}">
                        <svg x-show="open" x-transition.duration.500ms x-cloak xmlns="http://www.w3.org/2000/svg"
                            x-on:click="dropdown = true" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="absolute w-8 h-8 p-1 rounded-lg top-1 right-1 hover:bg-white/40">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                        </svg>
                        <ul x-cloak x-show="dropdown == true && id == '{{ $product->id }}' "
                            x-on:click.away="dropdown = false"
                            class="absolute right-0 z-50 p-1 font-sans text-white rounded-lg shadow-lg w-44 top-9 bg-zinc-900">
                            @if ($product->status == 3)
                                <button wire:click="unpinProduct('{{ $product->id }}')"
                                    class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">
                                    Unpin
                                </button>
                            @else
                                <button wire:click="pinProduct('{{ $product->id }}')"
                                    class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">
                                    Pin
                                </button>
                            @endif
                            @if ($product->product_image_2 && $product->preview == 0)
                                <button wire:click="previewBack('{{ $product->id }}')"
                                    class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">
                                    Preview (Back)
                                </button>
                            @elseif($product->product_image_2 && $product->preview == 1)
                                <button wire:click="previewFront('{{ $product->id }}')"
                                    class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">
                                    Preview (Front)
                                </button>
                            @endif
                            <button x-on:click="modal = true" wire:click="forceFill('{{ $product->id }}')"
                                class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">
                                Edit
                            </button>
                            <button wire:click="archiveProduct('{{ $product->id }}')"
                                class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">
                                Archive
                            </button>
                            @if ($product->visibility === 'public')
                                <button wire:click="setVisibility('{{ $product->id }}', 'private')"
                                    class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">Make private</button>
                            @else
                                <button wire:click="setVisibility('{{ $product->id }}', 'public')"
                                    class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">Make public</button>
                            @endif
                            <x-jet-modal-custom>
                                <form class="flex-col w-full space-y-4">
                                    <h2>Edit product</h2>
                                    <x-jet-label for="title" value="{{ __('Title') }}" />
                                    <x-jet-input id="title" class="block w-full mt-1" type="text" name="title"
                                        wire:model.defer="title" />
                                    @error('title')
                                        <p class="text-rose-500">{{ $message }}</p>
                                    @enderror
                                    <x-jet-label for="tags" value="{{ __('Tags') }}" />
                                    <x-jet-input id="tags" class="block w-full mt-1" type="text"
                                        wire:model.defer="tags" />
                                    @error('tags')
                                        <p class="text-rose-500">{{ $message }}</p>
                                    @enderror
                                    <p class="text-sm text-indigo-300">Price and commission are fixed by product type.</p>
                                    <x-jet-button type="button" wire:click="editProduct('{{ $product->id }}')"
                                        class="float-right">
                                        Save</x-jet-button>
                                </form>
                            </x-jet-modal-custom>
                            {{-- Delete product --}}
                            @if ($product->sold == 0 && $inCart[$product->id] == false)
                                <button wire:click="deleteProduct('{{ $product->id }}')"
                                    class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">
                                    Delete
                                </button>
                            @endif
                        </ul>
                        <div x-show="open" x-transition.duration.500ms x-cloak
                            class="absolute bottom-0 w-full p-2 text-sm text-center text-black rounded-xl bg-white/20 backdrop-blur">
                            <p class="">{{ $product->title }}</p>
                            <p><span class="font-bold">{{ $product->sold }} </span>sold</p>
                            <p>Price: <span
                                    class="px-1 rounded-lg bg-violet-500">{{ number_format($product->price, 2) }}</span>
                            </p>
                            <p>Commission: <span
                                    class="px-1 rounded-lg bg-violet-500">{{ number_format($product->commission, 2) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    {{-- Product Collection --}}
    <div x-cloak x-data="{ modal3: false, modal2: false, id: '' }" x-show="nav == 2" x-transition.opacity x-transition:enter.duration.500ms
        x-transition:leave.duration.100ms>
        <x-jet-button-custom x-on:click="modal3 = true">
            Add new collection
        </x-jet-button-custom>
        <x-jet-modal-custom-3>
            <form method="POST" action="{{ route('upload.collection') }}" enctype="multipart/form-data"
                class="flex-col w-full space-y-4">
                @csrf
                <x-jet-header>Create new collection</x-jet-header>
                <x-jet-label for="collection-image" value="{{ __('Collection Image') }}" />
                <input type="file" id="collection-image" name="collection_image"
                    wire:model.defer="collection_image" class="w-full">
                <x-jet-label for="title" value="{{ __('Title') }}" />
                <x-jet-input id="title" class="block w-full mt-1" type="text" name="title" />
                <x-jet-button class="float-right">Save</x-jet-button>
            </form>
        </x-jet-modal-custom-3>
        <div class="flex flex-col gap-4 my-4">
            @foreach ($productCollections as $item)
                <div x-on:click="modal2 = true"
                    style="background-image: url('{{ asset('storage/collection-image/' . $item->collection_image) }}')"
                    class="relative block overflow-hidden bg-center bg-no-repeat bg-cover md:rounded-xl">
                    <span
                        class="absolute z-10 inline-flex items-center px-3 py-1 font-semibold text-white bg-black rounded-full right-4 top-4">
                        {{ $item->product->count() }}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="ml-1.5 h-4 w-4 text-pink-500">
                            <path
                                d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.305 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z" />
                        </svg>
                    </span>
                    <button wire:click="deleteCollection('{{ $item->id }}')"
                        class="absolute z-10 inline-flex items-center px-4 py-1.5 font-semibold text-white transition bg-black rounded-full cursor-pointer hover:bg-red-500 right-4 top-12">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            wire:click="deleteCollection({{ $item->id }})" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                    <div class="relative p-8 pt-40 text-white bg-black bg-opacity-30">
                        <h3 class="px-6 py-2 text-xl tracking-wider rounded-full w-fit bg-black/70">
                            {{ $item->title }}</h3>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 p-2 md:grid-cols-4">
                    @foreach ($products as $product)
                        @unless ($product->collection_id != $item->id)
                            <p wire:click="removeFromCollection('{{ $product->id }}','{{ $item->id }}')"
                                class="p-2 rounded-md cursor-pointer bg-violet-500">
                                <span wire:loading.remove
                                    wire:target="removeFromCollection('{{ $product->id }}','{{ $item->id }}')">{{ $product->title }}</span>
                                <svg wire:loading
                                    wire:target="removeFromCollection('{{ $product->id }}','{{ $item->id }}')"
                                    viewBox="0 0 24 24" class="block w-6 h-6 m-auto -my-1 text-white animate-spin">
                                    <path fill="currentColor" d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z" />
                                </svg>
                            </p>
                        @endunless
                        @unless ($product->collection_id != null)
                            <p wire:click="addToCollection('{{ $product->id }}','{{ $item->id }}')"
                                class="p-2 bg-transparent rounded-md cursor-pointer ring-2 ring-rose-500">
                                <span wire:loading.remove
                                    wire:target="addToCollection('{{ $product->id }}','{{ $item->id }}')">{{ $product->title }}</span>
                                <svg wire:loading
                                    wire:target="addToCollection('{{ $product->id }}','{{ $item->id }}')"
                                    viewBox="0 0 24 24" class="block w-6 h-6 m-auto -my-1 text-rose-400 animate-spin">
                                    <path fill="currentColor" d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z" />
                                </svg>
                            </p>
                        @endunless
                    @endforeach
                </div>
                <x-jet-section-border />
            @endforeach
        </div>
    </div>
    {{-- If archive has items --}}
    @if (!$noArchives)
        <div class="flex flex-wrap justify-start gap-3 px-3 font-mono md:gap-5 md:px-0" x-cloak x-show="nav == 3"
            x-transition.opacity x-transition:enter.duration.500ms x-transition:leave.duration.100ms>
            @foreach ($archives as $archive)
                <div class="" x-data="{ open: false, dropdown: false, id: '{{ $archive->id }}' }" x-on:mouseenter="open = true"
                    x-on:mouseleave="open = false">
                    <div class="relative w-44 md:w-60">
                        <img class="object-cover object-center transition duration-500 shadow-md shadow-purple-500/30 rounded-xl"
                            src="{{ $archive->product_image }}">
                        <svg x-show="open" x-transition.duration.500ms x-cloak xmlns="http://www.w3.org/2000/svg"
                            x-on:click="dropdown = true" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="absolute w-8 h-8 p-1 rounded-lg top-1 right-1 hover:bg-white/40">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                        </svg>
                        <ul x-show="dropdown == true && id == '{{ $archive->id }}' "
                            x-on:click.away="dropdown = false"
                            class="absolute right-0 z-50 p-1 font-sans text-white rounded-lg shadow-lg w-36 top-9 bg-zinc-900">
                            <button wire:click="unarchiveProduct('{{ $archive->id }}')"
                                class="w-full px-4 py-2 text-left rounded-md hover:bg-indigo-500">
                                Unarchive
                            </button>
                        </ul>
                        <div x-show="open" x-transition.duration.500ms x-cloak
                            class="absolute bottom-0 w-full p-2 text-sm text-center text-black rounded-xl bg-white/20 backdrop-blur">
                            <p class="">{{ $archive->title }}</p>
                            <p><span class="font-bold">{{ $archive->sold }}
                                </span>sold
                            </p>
                            <p>Price: <span
                                    class="px-1 rounded-lg bg-violet-500">{{ number_format($archive->price, 2) }}</span>
                            </p>
                            <p>Commission: <span
                                    class="px-1 rounded-lg bg-violet-500">{{ number_format($archive->commission, 2) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script>
        FilePond.registerPlugin(FilePondPluginImagePreview);
        const fileInput = document.querySelector('input[id="collection-image"]');
        const pond = FilePond.create(fileInput);
        FilePond.setOptions({
            server: {
                url: '{{ route('upload') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });
    </script>
</div>
