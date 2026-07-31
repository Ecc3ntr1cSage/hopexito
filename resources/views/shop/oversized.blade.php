@section('title', 'Shop ' . ($productType ?? 'Sweatshirt') . ' | HopeXito')
<x-app-layout>
    <x-jet-whatsapp-contact />
    <div class="mx-auto mt-8 max-w-7xl">
        <x-jet-title>All Products</x-jet-title>
        <div class="flex items-center gap-2 ml-2 text-white">
            <a href="{{ route('explore') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-8 h-8 p-1 transition rounded-md hover:bg-indigo-500/50">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
            </svg>
            <a href="{{ route('shop.all') }}" class="p-1 px-2 transition rounded-md hover:bg-indigo-500/50">Shop
                All</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
            </svg>
            <a href="{{ route('shop.shirt') }}" class="p-1 px-2 transition rounded-md hover:bg-indigo-500/50">Standard
                Tee</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
            </svg>
            <p class="text-indigo-400">{{ $productType ?? 'Sweatshirt' }}</p>
        </div>
    </div>
    <div
        class="relative z-20 grid min-h-screen grid-cols-2 gap-2 px-1 mx-auto mt-6 pb-28 md:gap-6 sm:grid-cols-3 lg:grid-cols-4 max-w-7xl">
        @foreach ($products as $product)
            <a href="{{ route('product.show', $product->slug) }}" x-data="{ open: false }">
                <div
                    class="relative p-1 transition shadow-lg cursor-pointer group rounded-xl hover:shadow-fuchsia-500/50 bg-white/5 backdrop-filter backdrop-blur-3xl">
                    <div class="w-full overflow-hidden rounded-lg min-h-75" x-on:mouseenter="open = true"
                        x-on:mouseleave="open = false">
                        @if ($product->product_image_2 && $product->preview == 0)
                            <img src="{{ $product->product_image }}" alt="{{ $product->title }}" x-show="open == false"
                                class="w-full h-full transition ease-in-out rounded-t-lg">

                            <img x-cloak src="{{ $product->product_image_2 }}" alt="{{ $product->title }}"
                                x-show="open == true" class="w-full h-full transition ease-in-out rounded-t-lg">
                        @elseif($product->product_image_2 && $product->preview == 1)
                            <img src="{{ $product->product_image_2 }}" alt="{{ $product->title }}"
                                x-show="open == false" class="w-full h-full transition ease-in-out rounded-t-lg">
                            <img x-cloak src="{{ $product->product_image }}" alt="{{ $product->title }}"
                                x-show="open == true" class="w-full h-full transition ease-in-out rounded-t-lg">
                        @else
                            <img src="{{ $product->product_image }}" alt="{{ $product->title }}"
                                class="w-full h-full transition ease-in-out rounded-t-lg">
                        @endif
                    </div>
                    <div class="flex flex-col justify-between gap-1 px-2 py-1 tracking-wider md:px-4 md:py-2">
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
    <div class="mx-auto mb-20 max-w-7xl">
        {{ $products->links('/vendor/pagination/tailwind') }}
    </div>

</x-app-layout>
