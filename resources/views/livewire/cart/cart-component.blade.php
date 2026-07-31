@section('title', 'Cart | HopeXito')
<div class="mb-32 md:min-h-screen">
    <x-jet-session-message />
    <x-jet-whatsapp-contact />
    <div class="max-w-5xl mx-auto">
        <x-jet-gradient-card>
            <div class="w-full bg-black rounded-lg md:p-6">
                {{-- If no item in cart --}}
                @if ($cart->count() == 0)
                    <div class="w-full p-6 md:py-0">
                        <div
                            class="flex flex-col items-center justify-between gap-2 py-4 border-b border-indigo-500 md:flex-row">
                            <x-jet-title-small>Shopping Cart</x-jet-title-small>
                            <x-jet-button-custom onclick="window.location.href='{{ route('shop.all') }}'" class="">
                                Continue Shopping
                            </x-jet-button-custom>
                        </div>
                        <div class="mx-auto text-center text-indigo-300 md:m-12">
                            <img src="image\empty-cart.png" class="w-2/3 mx-auto">
                            <p>You don't have any items in your cart.</p>
                        </div>
                    </div>
                    {{-- If cart has items --}}
                @elseif ($cart->count() != 0)
                    <div class="w-full px-6">
                        <div
                            class="flex flex-col items-center justify-between gap-2 py-2 text-center border-b-2 border-indigo-500 md:flex-row">
                            <x-jet-title-small>
                                Shopping Cart
                            </x-jet-title-small>
                            <div class="flex flex-col items-center gap-2 md:gap-8 md:flex-row">
                                <p class="w-full text-sm tracking-wider text-indigo-400 uppercase md:w-1/4">
                                    {{ $cart->count() }}
                                    Item(s)
                                </p>
                                {{-- Checkout Button --}}
                                @if (Auth::check())
                                    <a href="{{ route('billplz-create') }}">
                                        <x-jet-button class="w-56 py-4">
                                            <p wire:loading.remove class="mx-auto text-xs">Checkout
                                                RM{{ number_format($total, 2) }}</p>
                                            <svg wire:loading viewBox="0 0 24 24"
                                                class="block w-6 h-6 m-auto -my-1 text-white animate-spin">
                                                <path fill="currentColor"
                                                    d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z" />
                                            </svg>
                                        </x-jet-button>
                                    </a>
                                @elseif(session()->has('delivery_info'))
                                    <a href="{{ route('billplz-create') }}">
                                        <x-jet-button class="w-56 py-4">
                                            <p wire:loading.remove class="mx-auto text-xs">Checkout
                                                RM{{ number_format($total, 2) }}</p>
                                            <svg wire:loading viewBox="0 0 24 24"
                                                class="block w-6 h-6 m-auto -my-1 text-white animate-spin">
                                                <path fill="currentColor"
                                                    d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z" />
                                            </svg>
                                        </x-jet-button>
                                    </a>
                                @else
                                    <a href="{{ route('guest.checkout') }}">
                                        <x-jet-button class="w-56 py-4">
                                            <p wire:loading.remove class="mx-auto text-xs">Checkout
                                                RM{{ number_format($total, 2) }}</p>
                                            <svg wire:loading viewBox="0 0 24 24"
                                                class="block w-6 h-6 m-auto -my-1 text-white animate-spin">
                                                <path fill="currentColor"
                                                    d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z" />
                                            </svg>
                                        </x-jet-button>
                                    </a>
                                @endif
                            </div>
                        </div>
                        {{-- Iterate through the items in cart --}}
                        @foreach ($cart as $cart)
                            <div
                                class="flex flex-col items-center gap-8 py-8 border-b border-indigo-500 md:px-6 md:flex-row">
                                @if (Auth::check())
                                    <a href="{{ route('product.show', $cart->cartProduct->slug) }}">
                                        <img class="w-56 transition shadow-lg md:w-40 rounded-xl hover:shadow-violet-500/30 hover:scale-105"
                                            src="{{ $cart->cartProduct->product_image }}" alt="" />
                                    </a>
                                @else
                                    <div>
                                        <img class="w-56 transition shadow-lg md:w-40 rounded-xl hover:shadow-violet-500/30 hover:scale-105"
                                            src="{{ $cart->options['product_image'] }}" alt="" />
                                    </div>
                                @endif

                                <div class="flex-grow text-center md:text-left">
                                    @if (Auth::check())
                                        <h2 class="mb-1 tracking-wider">{{ $cart->title }}</h2>
                                        <p class="mb-4 text-pink-500 uppercase">{{ $cart->size }}
                                            / {{ $cart->color }}</p>
                                    @else
                                        <h2 class="mb-1 tracking-wider">{{ $cart->name }}</h2>
                                        <p class="mb-4 text-pink-500 uppercase">{{ $cart->options['size'] }}
                                            / {{ $cart->options['color'] }}</p>
                                    @endif

                                    {{-- Delete item in cart --}}
                                    @if (Auth::check())
                                        <button type="submit" class="w-20 text-center rounded-full bg-rose-500"
                                            wire:click="destroyCart('{{ $cart->id }}')">
                                            Remove
                                        </button>
                                    @else
                                        <button type="submit" class="w-20 text-center rounded-full bg-rose-500"
                                            wire:click="destroyCart('{{ $cart->rowId }}')">
                                            Remove
                                        </button>
                                    @endif

                                </div>
                                {{-- Update cart items quantity --}}
                                <div class="flex gap-6">
                                    <div class="text-lg">
                                        <h2
                                            class="mb-4 py-0.5 text-xs tracking-wider text-center bg-indigo-500 rounded-xl">
                                            Quantity
                                        </h2>
                                        <div class="flex items-center">
                                            @if (Auth::check())
                                                @if ($discount == 1)
                                                    <button wire:click="decreaseQuantity('{{ $cart->id }}')"><svg
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="mx-2 cursor-pointer w-7 h-7 text-lime-400 active:scale-105">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19.5 12h-15" />
                                                        </svg></button>
                                                @else
                                                    <button type="button" disabled><svg
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="mx-2 w-7 h-7 text-lime-400 active:scale-105">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19.5 12h-15" />
                                                        </svg></button>
                                                @endif
                                            @else
                                                <button wire:click="decreaseQuantity('{{ $cart->rowId }}')"><svg
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="mx-2 cursor-pointer w-7 h-7 text-lime-400 active:scale-105">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 12h-15" />
                                                    </svg></button>
                                            @endif

                                            @if (Auth::check())
                                                <h1>{{ $cart->quantity }}</h1>
                                            @else
                                                <h1>{{ $cart->qty }}</h1>
                                            @endif
                                            @if (Auth::check())
                                                @if ($discount == 1)
                                                    <button wire:click="increaseQuantity('{{ $cart->id }}')"><svg
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="mx-2 cursor-pointer w-7 h-7 text-lime-400 active:scale-105">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 4.5v15m7.5-7.5h-15" />
                                                        </svg></button>
                                                @else
                                                    <button type="button" disabled><svg
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor"
                                                            class="mx-2 w-7 h-7 text-lime-400 active:scale-105">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 4.5v15m7.5-7.5h-15" />
                                                        </svg></button>
                                                @endif
                                            @else
                                                <button wire:click="increaseQuantity('{{ $cart->rowId }}')"><svg
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="mx-2 cursor-pointer w-7 h-7 text-lime-400 active:scale-105">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg></button>
                                            @endif


                                        </div>
                                    </div>
                                    {{-- Item Price --}}
                                    <div class="text-lg">
                                        <h2
                                            class="py-0.5 mb-4 text-xs tracking-wider text-center bg-indigo-500 rounded-xl">
                                            Price
                                        </h2>
                                        RM{{ number_format($cart->price, 2) }}
                                    </div>
                                    {{-- Item Subtotal --}}
                                    <div class="text-lg">
                                        <h2
                                            class="py-0.5 mb-4 text-xs tracking-wider text-center bg-indigo-500 rounded-xl">
                                            Subtotal
                                        </h2>
                                        <span class="text-lime-400">RM{{ number_format($cart->subtotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-jet-gradient-card>
    </div>
    {{-- @if (Auth::check())
        <div class="max-w-5xl mx-auto mt-8">
            <x-jet-gradient-card>
                <div class="p-6">
                    <div class="w-full lg:px-6">
                        <div
                            class="flex flex-col items-center justify-between gap-2 py-2 text-center border-b border-indigo-500 md:flex-row">
                            <x-jet-title-small>
                                Grab Limited Time Coupon
                            </x-jet-title-small>
                            <div class="flex flex-col items-center gap-2 md:flex-row">
                                <p>Expire In:</p>
                                <p id="demo" class="px-3 py-1 bg-indigo-500 rounded-md">
                                </p>
                            </div>
                        </div>
                        <div class="grid max-w-3xl grid-cols-3 gap-4 mx-auto my-4">
                            <div class="relative">
                                @if ($discount != 0.9 && $total >= 60)
                                    <button type="button" wire:click="discount('0.9')"
                                        class="flex items-center justify-center w-full h-16 rounded-md bg-violet-500">
                                        10% Off
                                    </button>
                                @elseif($discount == 0.9)
                                    <button type="button" wire:click="removeDiscount('1')"
                                        class="flex items-center justify-center w-full h-16 bg-transparent border-2 rounded-md border-violet-500">
                                        Applied
                                    </button>
                                @else
                                    <button type="button" disabled
                                        class="flex items-center justify-center w-full h-16 bg-gray-500 rounded-md cursor-not-allowed">
                                        10% Off
                                    </button>
                                @endif
                                @if ($total >= 60)
                                    <p class="absolute left-0 right-0 p-2 text-xs text-center -bottom-12 md:-bottom-7 text-lime-500">
                                        Minimum spend: RM60
                                    </p>
                                @else
                                    <p class="absolute left-0 right-0 p-2 text-xs text-center -bottom-12 md:-bottom-7 text-rose-500">
                                        Minimum spend: RM60
                                    </p>
                                @endif
                            </div>
                            <div class="relative">
                                @if ($discount != 0.85 && $total >= 90)
                                    <button type="button" wire:click="discount('0.85')"
                                        class="flex items-center justify-center w-full h-16 rounded-md bg-violet-500">
                                        15% Off
                                    </button>
                                @elseif($discount == 0.85)
                                    <button type="button" wire:click="removeDiscount('1')"
                                        class="flex items-center justify-center w-full h-16 bg-transparent border-2 rounded-md border-violet-500">
                                        Applied
                                    </button>
                                @else
                                    <button type="button" disabled
                                        class="flex items-center justify-center w-full h-16 bg-gray-500 rounded-md cursor-not-allowed">
                                        15% Off
                                    </button>
                                @endif
                                @if ($total >= 90)
                                    <p class="absolute left-0 right-0 p-2 text-xs text-center -bottom-12 md:-bottom-7 text-lime-500">
                                        Minimum spend: RM90
                                    </p>
                                @else
                                    <p class="absolute left-0 right-0 p-2 text-xs text-center -bottom-12 md:-bottom-7 text-rose-500">
                                        Minimum spend: RM90
                                    </p>
                                @endif
                            </div>
                            <div class="relative">
                                @if ($discount != 0.8 && $total >= 120)
                                    <button type="button" wire:click="discount('0.80')"
                                        class="flex items-center justify-center w-full h-16 rounded-md bg-violet-500">
                                        20% Off
                                    </button>
                                @elseif($discount == 0.8)
                                    <button type="button" wire:click="removeDiscount('1')"
                                        class="flex items-center justify-center w-full h-16 bg-transparent border-2 rounded-md border-violet-500">
                                        Applied
                                    </button>
                                @else
                                    <button type="button" disabled
                                        class="flex items-center justify-center w-full h-16 bg-gray-500 rounded-md cursor-not-allowed">
                                        20% Off
                                    </button>
                                @endif
                                @if ($total >= 120)
                                    <p class="absolute left-0 right-0 p-2 text-xs text-center -bottom-12 md:-bottom-7 text-lime-500">
                                        Minimum spend: RM120
                                    </p>
                                @else
                                    <p class="absolute left-0 right-0 p-2 text-xs text-center -bottom-12 md:-bottom-7 text-rose-500">
                                        Minimum spend: RM120
                                    </p>
                                @endif
                            </div>
                        </div>
            </x-jet-gradient-card>
        </div>
    @endif --}}
</div>
@if (Auth::check())
    @if ($cart->count() != 0)
        <div class="mt-10">
            <x-jet-gradient-card>
                <div class="w-full bg-black rounded-lg md:p-6">
                    <div class="w-full px-6">
                        <div
                            class="flex flex-col items-center justify-between gap-2 py-2 text-center border-b-2 border-indigo-500 md:flex-row">
                            <x-jet-title-small>
                                You May Also Like
                            </x-jet-title-small>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mx-auto mt-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @foreach ($products as $product)
                                <a href="{{ route('product.show', $product->slug) }}" x-data="{ open: false }">
                                    <div
                                        class="relative p-1 transition shadow-lg cursor-pointer group rounded-xl hover:shadow-fuchsia-500/50 bg-white/5 backdrop-filter backdrop-blur-3xl">
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
                                                <img src="{{ $product->product_image }}" alt="{{ $product->title }}"
                                                    class="w-full h-full transition ease-in-out rounded-t-lg">
                                            @endif
                                        </div>
                                        <div
                                            class="flex flex-col justify-between px-2 py-1 tracking-wider md:px-4 md:py-2">
                                            @if ($product->category == 'Shirt')
                                                <p class="px-3 py-0.5 mt-1 bg-fuchsia-700/80 rounded-md w-fit text-xs">
                                                    Standard
                                                    Tee</p>
                                            @elseif(in_array($product->category, ['Sweatshirt', 'Hoodie']))
                                                <p class="px-3 py-0.5 mt-1 rounded-md bg-indigo-700/80 w-fit text-xs">
                                                    {{ $product->category }}
                                                    Tee</p>
                                            @else
                                                <p></p>
                                            @endif
                                            <div class="mt-2 text-sm text-white truncate md:font-medium">
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
        </div>
    @endif
@endif
<script>
    // Set the date we're counting down to
    var countDownDate = new Date("Apr 21, 2023 23:59:59").getTime();

    // Update the count down every 1 second
    var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Display the result in the element with id="demo"
        document.getElementById("demo").innerHTML = days + "d " + hours + "h " +
            minutes + "m " + seconds + "s ";

        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("demo").innerHTML = "EXPIRED";
        }
    }, 1000);
</script>
</div>
</div>
