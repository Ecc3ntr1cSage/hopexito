@section('title', $search . ' | HopeXito')
<x-app-layout>
    <x-jet-whatsapp-contact />
    <div class="relative z-20 min-h-screen px-1 mx-auto mt-6 pb-28 max-w-7xl">
        <p class="tracking-wide text-md"><span class="px-1 text-indigo-400 bg-black">{{ $user_count }} results</span>
            for Users</p>
        <div class="grid grid-cols-1 gap-2 mt-8 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
            @foreach ($users as $user)
                <a href="{{ route('people', $user->name) }}" class="relative group">
                    <div
                        class="absolute transition duration-1000 rounded-xl opacity-25 -inset-1 bg-gradient-to-r from-purple-600 to-pink-600 blur group-hover:opacity-100 group-hover:duration-200">
                    </div>
                    <div class="relative flex items-center gap-3 px-6 py-4 bg-black rounded-xl">
                        <img class="object-cover w-16 h-16 rounded-full " src="{{ $user->profile_photo_url }}"
                            alt="{{ $user->name }}" />
                        <p class="text-white">{{ $user->name }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <x-jet-section-border />
        <p class="tracking-wide text-md ">We found <span class="px-1 text-indigo-400 bg-black">{{ $product_count }}
                Products</span> that matched <span class="px-1 text-indigo-400 bg-black">"{{ $search }}"</span>
        </p>
        <div class="grid grid-cols-2 gap-2 mt-8 sm:grid-cols-3 lg:grid-cols-4 md:gap-6">
            @foreach ($products as $product)
                <a href="{{ route('product.show', $product->slug) }}" x-data="{ open: false }">
                    <div
                        class="relative p-1 transition shadow-lg cursor-pointer group rounded-xl hover:shadow-fuchsia-500/50 bg-white/5 backdrop-filter backdrop-blur-3xl">
                        <div class="product-media-frame rounded-lg" x-on:mouseenter="open = true"
                            x-on:mouseleave="open = false">
                            @if ($product->product_card_hover_image)
                                <img src="{{ $product->product_card_image }}" alt="{{ $product->title }}"
                                    x-show="open == false" class="product-media-image transition ease-in-out rounded-t-lg">

                                <img x-cloak src="{{ $product->product_card_hover_image }}" alt="{{ $product->title }}"
                                    x-show="open == true" class="product-media-image transition ease-in-out rounded-t-lg">
                            @else
                                <img src="{{ $product->product_card_image }}" alt="{{ $product->title }}"
                                    class="product-media-image transition ease-in-out rounded-t-lg">
                            @endif
                        </div>
                        <div class="flex flex-col justify-between px-2 py-1 tracking-wider md:px-4 md:py-2">
                            @if ($product->category == 'Shirt')
                                <p class="px-3 py-0.5 bg-fuchsia-700/80 rounded-md w-fit text-xs">Shirt</p>
                            @elseif(in_array($product->category, ['Sweatshirt', 'Hoodie']))
                                <p class="px-3 py-0.5 rounded-md bg-indigo-700/80 w-fit text-xs">{{ $product->category }}</p>
                            @else
                                <p></p>
                            @endif
                            <div class="mt-1 text-sm text-white truncate md:font-medium">
                                {{ $product->title }}
                            </div>
                            <h2 class="hover:text-fuchsia-500">By {{ $product->shopname }}
                            </h2>
                            <h2 class="m-1 text-lg text-center md:m-2 text-fuchsia-500">
                                RM{{ number_format($product->price, 2) }}</h2>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
