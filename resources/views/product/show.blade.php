@section('title', $product->title . ' | HopeXito')
@section('thumbnail', $product->product_image)
<x-app-layout>
    <x-jet-whatsapp-contact />
    <section class="min-h-screen pb-12 text-gray-700 bg-neutral-900" x-data="{ preview: 1, size: '', color: '{{ $colors->first() ?? 'White' }}', variants: @js($variantData) }">
        <x-jet-session-message />
        <div class="py-4 mx-auto">
            <div class="flex flex-wrap gap-3 mx-auto lg:w-4/5">
                <div class="flex items-center gap-3 mx-2 text-white">
                    <a href="{{ route('explore') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-8 h-8 p-1 transition rounded-md hover:bg-indigo-500/50">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </a>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <a href="{{ route('people', $product->shopname) }}"
                        class="p-1 px-2 transition rounded-md hover:bg-indigo-500/50">{{ $product->shopname }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <p class="text-indigo-400">{{ $product->title }}</p>
                </div>
                <div class="relative max-w-screen-xl mx-auto md:px-4">
                    <div class="grid gap-8 md:grid-cols-4 lg:items-start">
                        <div class="col-span-4 md:col-span-2">
                            <div class="relative" x-show="preview == 1" x-transition:enter.duration.500ms>
                                <div class="w-full overflow-hidden rounded-lg" id="product-image">
                                    <img class="h-full sm:w-full w-96" x-bind:src="variants[color]?.front || '{{ $product->product_image }}'">
                                </div>
                            </div>
                            <div x-cloak class="relative" x-show="preview == 2" x-transition:enter.duration.500ms>
                                <div class="w-full overflow-hidden rounded-lg">
                                    <img class="h-full sm:w-full w-96" x-bind:src="variants[color]?.back || '{{ $product->product_image_2 }}'">
                                </div>
                            </div>
                            <div class="flex gap-3 px-2 mt-3 cursor-pointer">
                                <img alt="Tee" src="{{ $product->product_image }}" x-on:click="preview = 1"
                                    class="object-cover rounded-md lg:h-36 h-28 w-28 lg:w-36"
                                    x-bind:class="preview == 1 ? 'ring ring-indigo-500' : ''" />
                                @if ($product->product_image_2)
                                    <img alt="Tee" src="{{ $product->product_image_2 }}" x-on:click="preview = 2"
                                        class="object-cover rounded-md lg:h-36 w-28 h-28 lg:w-36"
                                        x-bind:class="preview == 2 ? 'ring ring-indigo-500' : ''" />
                                @endif
                                @if ($product->category == 'Shirt')
                                    <div class="flex flex-col gap-2 text-white md:ml-auto" x-data="{ modal: false }">
                                        <p class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                                            100% Cotton
                                        </p>
                                        <p class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-fuchsia-500"></span>
                                            180 gsm
                                        </p>
                                        <x-jet-button-custom type="button" x-on:click="modal = true">
                                            Size Chart
                                        </x-jet-button-custom>
                                        <x-jet-modal-custom x-show="modal == true">
                                            <div class="flex flex-col w-full gap-2">
                                                <div class="flex gap-2 p-2 text-center bg-black/50 rounded-xl">
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">Size</p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">Shoulder</p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">Chest</p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">Sleeve</p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">Length</p>
                                                </div>
                                                <div
                                                    class="flex items-center p-2 px-2 text-center bg-black/50 rounded-xl">
                                                    <p class="basis-1/5 px-2 py-0.5 bg-rose-500 rounded-md">XS</p>
                                                    <p class="basis-1/5">15"</p>
                                                    <p class="basis-1/5">36"</p>
                                                    <p class="basis-1/5">7.5"</p>
                                                    <p class="basis-1/5">26"</p>
                                                </div>
                                                <div class="flex items-center p-2 text-center bg-black/50 rounded-xl ">
                                                    <p class="basis-1/5 px-2 py-0.5 bg-rose-500 rounded-md">S</p>
                                                    <p class="basis-1/5">16"</p>
                                                    <p class="basis-1/5">38"</p>
                                                    <p class="basis-1/5">8"</p>
                                                    <p class="basis-1/5">27"</p>
                                                </div>
                                                <div class="flex items-center p-2 text-center bg-black/50 rounded-xl ">
                                                    <p class="basis-1/5 px-2 py-0.5 bg-rose-500 rounded-md">M</p>
                                                    <p class="basis-1/5">17"</p>
                                                    <p class="basis-1/5">40"</p>
                                                    <p class="basis-1/5">8.5"</p>
                                                    <p class="basis-1/5">28"</p>
                                                </div>
                                                <div class="flex items-center p-2 text-center bg-black/50 rounded-xl ">
                                                    <p class="basis-1/5 px-2 py-0.5 bg-rose-500 rounded-md">L</p>
                                                    <p class="basis-1/5">18"</p>
                                                    <p class="basis-1/5">42"</p>
                                                    <p class="basis-1/5">9"</p>
                                                    <p class="basis-1/5">29"</p>
                                                </div>
                                                <div class="flex items-center p-2 text-center bg-black/50 rounded-xl ">
                                                    <p class="basis-1/5 px-2 py-0.5 bg-rose-500 rounded-md">XL</p>
                                                    <p class="basis-1/5">19"</p>
                                                    <p class="basis-1/5">44"</p>
                                                    <p class="basis-1/5">9.5"</p>
                                                    <p class="basis-1/5">30"</p>
                                                </div>
                                                <div class="flex items-center p-2 text-center bg-black/50 rounded-xl">
                                                    <p class="basis-1/5 px-2 py-0.5 bg-rose-500 rounded-md">2XL</p>
                                                    <p class="basis-1/5">20"</p>
                                                    <p class="basis-1/5">46"</p>
                                                    <p class="basis-1/5">10"</p>
                                                    <p class="basis-1/5">31"</p>
                                                </div>
                                            </div>
                                        </x-jet-modal-custom>
                                    </div>
                                @elseif(in_array($product->category, ['Sweatshirt', 'Hoodie']))
                                    <div class="flex flex-col gap-2 ml-auto text-white" x-data="{ modal: false }">
                                        <p class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                                            100% Cotton
                                        </p>
                                        <p class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-fuchsia-500"></span>
                                            180 gsm
                                        </p>
                                        <x-jet-button-custom type="button" x-on:click="modal = true">
                                            Size Chart
                                        </x-jet-button-custom>
                                        <x-jet-modal-custom x-show="modal == true">
                                            <div class="flex flex-col w-full gap-2">
                                                <div class="flex gap-2 p-2 text-center bg-black/50 rounded-xl">
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">Size</p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">S
                                                    </p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">M</p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">L
                                                    </p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">XL</p>
                                                    <p class="px-2 py-1 bg-indigo-500 rounded-md basis-1/5">2XL</p>
                                                </div>
                                                <div class="flex items-center p-2 text-center bg-black/50 rounded-xl ">
                                                    <p class="basis-1/5 px-2 py-0.5 bg-rose-500 rounded-md">Width</p>
                                                    <p class="basis-1/5">54cm</p>
                                                    <p class="basis-1/5">57cm</p>
                                                    <p class="basis-1/5">60cm</p>
                                                    <p class="basis-1/5">64cm</p>
                                                    <p class="basis-1/5">68cm</p>
                                                </div>
                                                <div class="flex items-center p-2 text-center bg-black/50 rounded-xl ">
                                                    <p class="basis-1/5 px-2 py-0.5 bg-rose-500 rounded-md">Length</p>
                                                    <p class="basis-1/5">66cm</p>
                                                    <p class="basis-1/5">69cm</p>
                                                    <p class="basis-1/5">75cm</p>
                                                    <p class="basis-1/5">76cm</p>
                                                    <p class="basis-1/5">78cm</p>
                                                </div>
                                            </div>
                                        </x-jet-modal-custom>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="relative flex flex-col w-full col-span-4 p-6 md:col-span-2">
                            <div class="z-10 md:py-12">
                                <p class="my-0.5 text-2xl font-medium text-indigo-400 ">{{ $product->title }}</p>
                                <div class="flex items-center space-x-8">
                                    <p class="text-xs text-gray-300">Designed by <a
                                            href="{{ route('people', $product->shopname) }}"><span
                                                class="text-sm font-bold tracking-wider text-indigo-400 hover:text-indigo-500">{{ $product->shopname }}</span></a>
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 my-3 w-96">
                                    @foreach (preg_split('/[,，]/u', $product->tags) as $tag)
                                        <p class="px-2 py-0.5 bg-violet-500 rounded-md text-white">{{ $tag }}
                                        </p>
                                    @endforeach
                                </div>
                                <h1 class="my-2 text-2xl font-medium tracking-wider text-violet-400">
                                    RM{{ number_format($product->price, 2) }}
                                    @if (Auth::check() && $product->isOwnedBy(Auth::user()))
                                        <span class="text-sm text-lime-400">(RM{{ number_format($product->price * .85, 2) }} for you)</span>
                                    @endif
                                </h1>
                                <form action="{{ route('cart.store') }}" method="POST" class="mt-6">
                                    @csrf
                                    <div class="flex items-center gap-2">
                                        <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                        <h2 class="text-indigo-300">Choose Size</h2>
                                        @error('size')
                                            <p class="w-2 h-2 rounded-full bg-rose-500"></p>
                                        @enderror
                                    </div>
                                    <div class="flex flex-row gap-2 my-2">
                                        <div class="w-14">
                                            <input type="radio" name="size" id="XS" value="XS"
                                                class="hidden" x-on:click="size = 'XS'" x-model="size" />
                                            <label for="XS"
                                                :class="size == 'XS' ? 'border-indigo-500 text-lime-400 transition' :
                                                    'border-white '"
                                                class="grid p-2 text-sm text-white transition border-2 cursor-pointer place-items-center hover:scale-105">XS</label>
                                        </div>
                                        <div class="w-14">
                                            <input type="radio" name="size" id="S" value="S"
                                                class="hidden" x-on:click="size = 'S'" x-model="size" />
                                            <label for="S"
                                                :class="size == 'S' ? 'border-indigo-500 text-lime-400 transition' :
                                                    'border-white '"
                                                class="grid p-2 text-sm text-white transition border-2 cursor-pointer place-items-center hover:scale-105">S</label>
                                        </div>
                                        <div class="w-14">
                                            <input type="radio" name="size" id="M" value="M"
                                                class="hidden" x-on:click="size = 'M'" x-model="size" />
                                            <label for="M"
                                                :class="size == 'M' ? 'border-indigo-500 text-lime-400 transition' :
                                                    'border-white '"
                                                class="grid p-2 text-sm text-white transition border-2 cursor-pointer place-items-center hover:scale-105">M</label>
                                        </div>
                                        <div class="w-14">
                                            <input type="radio" name="size" id="L" value="L"
                                                class="hidden" x-on:click="size = 'L'" x-model="size" />
                                            <label for="L"
                                                :class="size == 'L' ? 'border-indigo-500 text-lime-400 transition' :
                                                    'border-white '"
                                                class="grid p-2 text-sm text-white transition border-2 cursor-pointer place-items-center hover:scale-105">L</label>
                                        </div>
                                        <div class="w-14">
                                            <input type="radio" name="size" id="XL" value="XL"
                                                class="hidden" x-on:click="size = 'XL'" x-model="size" />
                                            <label for="XL"
                                                :class="size == 'XL' ? 'border-indigo-500 text-lime-400 transition' :
                                                    'border-white '"
                                                class="grid p-2 text-sm text-white transition border-2 cursor-pointer place-items-center hover:scale-105">XL</label>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-indigo-300">Choose Color</h2>
                                        @error('color')
                                            <p class="w-2 h-2 rounded-full bg-rose-500"></p>
                                        @enderror
                                    </div>
                                    <div class="flex flex-row gap-2 my-2">
                                        @foreach ($colors as $color)
                                            <div class="w-20">
                                                <input type="radio" name="color" id="{{ $color }}"
                                                    value="{{ $color }}" class="hidden"
                                                    x-on:click="color = '{{ $color }}'" x-model="color" />
                                                <label for="{{ $color }}"
                                                    :class="color == '{{ $color }}' ?
                                                        'border-indigo-500 text-lime-400 transition' :
                                                        'border-white '"
                                                    class="grid p-2 text-sm text-white transition border-2 cursor-pointer place-items-center hover:scale-105">{{ $color }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex flex-col gap-4 my-6 md:flex-row">
                                        <div class="flex items-center justify-between px-4 rounded-md w-80 sm:w-auto md:px-0 ring-4 ring-indigo-500"
                                            x-data="{
                                                quantity: 1,
                                                minus(value) {
                                                    this.quantity = parseInt(this.quantity);
                                                    (this.quantity == 1) ? this.quantity = 1: this.quantity -= value;
                                                },
                                                plus(value) {
                                                    this.quantity = parseInt(this.quantity);
                                                    this.quantity += value;
                                                }
                                            }">
                                            <svg x-on:click="minus(1)" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor"
                                                class="m-3 cursor-pointer w-7 h-7 text-lime-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 12h-15" />
                                            </svg>
                                            <input type="text" name="quantity" x-model="quantity"
                                                class="w-16 text-lg text-center text-white bg-transparent border-none" />
                                            <svg x-on:click="plus(1)" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor"
                                                class="m-3 cursor-pointer w-7 h-7 text-lime-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-4">
                                        <x-jet-button name="add_to_cart" class="py-4 mt-3 w-80 md:mt-0">
                                            <span class="mx-auto">Add to Cart</span>
                                        </x-jet-button>
                                        <button type="submit" name="buy_now" class="relative group w-80">
                                                <div
                                                    class="absolute transition duration-1000 rounded-lg opacity-25 -inset-1 bg-gradient-to-r from-purple-600 to-pink-600 blur group-hover:opacity-100 group-hover:duration-200">
                                                </div>
                                                <div
                                                    class="relative flex items-center justify-between px-6 py-4 text-white bg-black rounded-lg">
                                                    <span
                                                        class="mx-auto font-sans text-xs font-semibold tracking-widest uppercase">Buy
                                                        Now</span>
                                                </div>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <x-jet-gradient-card>
        <div class="min-h-screen py-8 bg-black/90 rounded-xl">
            <div class="relative w-full px-2 py-6 mx-auto lg:max-w-7xl">
                <x-jet-title>
                    From the same designer
                </x-jet-title>
                <div class="grid grid-cols-2 gap-2 mx-auto mt-6 md:gap-6 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($products as $product)
                        <a href="{{ route('product.show', $product->slug) }}" x-data="{ open: false }">
                            <div
                                class="relative p-1 transition shadow-lg cursor-pointer group rounded-xl hover:shadow-fuchsia-500/50 bg-white/5 backdrop-filter backdrop-blur-3xl">
                                <div class="w-full overflow-hidden rounded-lg min-h-75" x-on:mouseenter="open = true"
                                    x-on:mouseleave="open = false">
                                    @if ($product->product_image_2)
                                        <img src="{{ $product->product_image }}" alt="{{ $product->title }}"
                                            x-show="open == false"
                                            class="w-full h-full transition ease-in-out rounded-t-lg">

                                        <img src="{{ $product->product_image_2 }}" alt="{{ $product->title }}"
                                            x-show="open == true"
                                            class="w-full h-full transition ease-in-out rounded-t-lg">
                                    @else
                                        <img src="{{ $product->product_image }}" alt="{{ $product->title }}"
                                            class="w-full h-full transition ease-in-out rounded-t-lg">
                                    @endif
                                </div>
                                <div class="flex flex-col justify-between px-2 py-1 tracking-wider md:px-4 md:py-2">
                                    @if ($product->category == 'Shirt')
                                        <p class="px-3 py-0.5 bg-fuchsia-700/80 rounded-md w-fit text-xs">Shirt
                                        </p>
                                    @elseif(in_array($product->category, ['Sweatshirt', 'Hoodie']))
                                        <p class="px-3 py-0.5 rounded-md bg-indigo-700/80 w-fit text-xs">{{ $product->category }}
                                        </p>
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
        </div>
    </x-jet-gradient-card>
    <x-jet-gradient-card>
        <div class="py-8 bg-black/90 rounded-xl">
            <div class="relative w-full px-2 py-6 mx-auto lg:max-w-7xl">
                <x-jet-title>Discover other products</x-jet-title>
                <div class="grid grid-cols-2 gap-2 mx-auto mt-6 md:gap-6 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($discover as $item)
                        <a href="{{ route('product.show', $item->slug) }}" x-data="{ open: false }">
                            <div
                                class="relative p-1 transition shadow-lg cursor-pointer group rounded-xl hover:shadow-fuchsia-500/50 bg-white/5 backdrop-filter backdrop-blur-3xl">
                                <div class="w-full overflow-hidden rounded-lg min-h-75" x-on:mouseenter="open = true"
                                    x-on:mouseleave="open = false">
                                    @if ($item->product_image_2)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->title }}"
                                            x-show="open == false"
                                            class="w-full h-full transition ease-in-out rounded-t-lg">

                                        <img src="{{ $item->product_image_2 }}" alt="{{ $item->title }}"
                                            x-show="open == true"
                                            class="w-full h-full transition ease-in-out rounded-t-lg">
                                    @else
                                        <img src="{{ $item->product_image }}" alt="{{ $item->title }}"
                                            class="w-full h-full transition ease-in-out rounded-t-lg">
                                    @endif
                                </div>
                                <div class="flex flex-col justify-between px-2 py-1 tracking-wider md:px-4 md:py-2">
                                    @if ($product->category == 'Shirt')
                                        <p class="px-3 py-0.5 bg-fuchsia-700/80 rounded-md w-fit text-xs">Shirt
                                        </p>
                                    @elseif(in_array($product->category, ['Sweatshirt', 'Hoodie']))
                                        <p class="px-3 py-0.5 rounded-md bg-indigo-700/80 w-fit text-xs">{{ $product->category }}
                                        </p>
                                    @else
                                        <p></p>
                                    @endif
                                    <div class="mt-1 text-sm text-white truncate md:font-medium">
                                        {{ $item->title }}
                                    </div>
                                    <h2 class="hover:text-fuchsia-500">By {{ $item->shopname }}
                                    </h2>
                                    <h2 class="m-1 text-lg text-center md:m-2 text-fuchsia-500">
                                        RM{{ number_format($item->price, 2) }}</h2>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </x-jet-gradient-card>
</x-app-layout>
