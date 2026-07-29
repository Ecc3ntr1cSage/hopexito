@section('title', 'Designers Collection | HopeXito')
<x-app-layout>
    <x-jet-whatsapp-contact />
    <div class="w-full min-h-screen px-0 mx-auto mt-8 mb-32 lg:max-w-7xl">
        <x-jet-title>Designers Collection</x-jet-title>
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
            <a href="{{ route('shop.all') }}" class="p-1 px-2 transition rounded-md hover:bg-indigo-500/50">Shop All</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
            </svg>
            <p class="text-indigo-400">Explore Collection</p>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
            </svg>
            <a href="{{ route('shop.shirt') }}"
                class="p-1 px-2 transition rounded-md hover:bg-indigo-500/50">Standard Tee</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
            </svg>
            <a href="{{ route('shop.oversized') }}"
                class="p-1 px-2 transition rounded-md hover:bg-indigo-500/50">Oversized Tee</a>
        </div>
        <div>
            <div class="relative flex flex-col gap-4 my-4 mt-12">
                @foreach ($productsCollection as $item)
                    <div style="background-image: url('{{ asset('storage/collection-image/' . $item->collection_image) }}')"
                        class="relative block overflow-hidden bg-center bg-no-repeat bg-cover md:rounded-xl ">
                        <span
                            class="absolute z-10 items-center hidden px-3 py-1 font-semibold text-white bg-black rounded-full md:inline-flex right-4 top-4">
                            {{ $item->product->count() }}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="ml-1.5 h-4 w-4 text-pink-500">
                                <path
                                    d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.305 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z" />
                            </svg>
                        </span>
                        <div class="relative text-white bg-black md:p-8 h-96 bg-opacity-30">
                            <h3
                                class="px-6 py-2 mt-4 text-lg tracking-wider rounded-full md:text-2xl bg-black/80 w-fit">
                                {{ $item->title }}</h3>
                        </div>
                    </div>
                    <div class="max-w-5xl mx-auto md:-translate-y-60 -translate-y-72 -mb-28">
                        <div class="p-4 rounded-xl shadow-lg bg-clip-padding backdrop-filter backdrop-blur-md bg-gradient-to-r from-rose-400/10 via-fuchsia-500/10 to-indigo-500/10 shadow-violet-500/50">
                            <div class="grid grid-cols-2 gap-2 mx-auto mt-6 sm:grid-cols-3 lg:grid-cols-4">
                                @foreach ($item->product as $product)
                                    <a href="{{ route('product.show', $product->slug) }}" x-data="{ open: false }">
                                        <div
                                            class="relative p-1 transition shadow-lg cursor-pointer group rounded-xl hover:shadow-fuchsia-500/50 bg-black/10 backdrop-filter backdrop-blur-3xl">
                                            <div class="w-full overflow-hidden rounded-lg min-h-75"
                                                x-on:mouseenter="open = true" x-on:mouseleave="open = false">
                                                @if ($product->product_image_2 && $product->preview == 0)
                                                    <img src="{{ $product->product_image }}" alt="{{ $product->title }}"
                                                        x-show="open == false"
                                                        class="w-full h-full transition ease-in-out rounded-t-lg">
                                                    <img x-cloak src="{{ $product->product_image_2 }}"
                                                        alt="{{ $product->title }}" x-show="open == true"
                                                        class="w-full h-full transition ease-in-out rounded-t-lg">
                                                @elseif($product->product_image_2 && $product->preview == 1)
                                                    <img src="{{ $product->product_image_2 }}"
                                                        alt="{{ $product->title }}" x-show="open == false"
                                                        class="w-full h-full transition ease-in-out rounded-t-lg">
                                                    <img x-cloak src="{{ $product->product_image }}"
                                                        alt="{{ $product->title }}" x-show="open == true"
                                                        class="w-full h-full transition ease-in-out rounded-t-lg">
                                                @else
                                                    <img src="{{ $product->product_image }}"
                                                        alt="{{ $product->title }}"
                                                        class="w-full h-full transition ease-in-out rounded-t-lg">
                                                @endif
                                            </div>
                                            <div
                                                class="flex flex-col justify-between px-2 py-1 tracking-wider md:px-4 md:py-2">
                                                <div class="text-sm text-white truncate md:font-medium">
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
                    </div>
                @endforeach
            </div>
        </div>
        <div class="mx-auto mb-20 max-w-7xl">
            {{ $productsCollection->links('/vendor/pagination/tailwind') }}
        </div>
    </div>
</x-app-layout>
