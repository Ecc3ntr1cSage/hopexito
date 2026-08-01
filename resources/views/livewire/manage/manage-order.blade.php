@inject('carbon', 'Carbon\Carbon')
@section('title', 'Order History | HopeXito')
<div class="max-w-3xl min-h-screen mx-auto mt-8 mb-32 text-white">
    <x-jet-session-message/>
    <h1 class="px-2 text-2xl font-bold text-transparent md:px-0 lg:text-3xl bg-clip-text bg-gradient-to-r from-red-400 to-indigo-800">
        Your Orders</h1>
    <p class="px-2 my-2 mb-8 md:px-0">Check the status of recent orders, manage returns, and discover similar products.</p>
    @foreach ($order as $order)
        <div class="mb-12">
            <div class="flex flex-col px-2 mb-4 font-mono md:px-6 md:flex-row md:items-center md:justify-between">
                <p class="">Order <span class="text-indigo-400 uppercase">#{{ $order->id }}</span></p>
                <p class="">Order placed on <span
                        class="text-indigo-400">{{ $carbon::parse($order->paid_at)->format('F d, Y g:i A') }}</span>
                </p>
                <p class="">Total <span
                        class="text-indigo-400">RM{{ number_format($order->amount, 2) }}</span></p>
            </div>
            <div class="flex flex-col transition shadow-md md:flex-row bg-black/30 rounded-xl hover:shadow-indigo-500/30">
                <div class="flex-col w-full md:w-3/5">
                    @foreach ($order->productOrder as $item)
                        <div class="flex p-4 space-x-8">
                            <img class="w-40 transition rounded-lg" src="{{ $item->product->product_card_image }}" alt="" />
                            <div class="relative flex-col w-full space-y-1">
                                <p class="text-indigo-400">{{ $item->title }}</p>
                                <p class= uppercase">{{ $item->size }} / {{ $item->color }}
                                </p>
                                <p class="tracking-wider text-fuchsia-400">RM{{ number_format($item->price, 2) }} x
                                    {{ $item->quantity }}</p>
                                <div class="absolute bottom-2">
                                    <x-jet-button-custom onclick="window.location.href='{{ route('product.show', $item->product->slug) }}'">
                                        Buy Again
                                    </x-jet-button-custom>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="relative flex-col w-full p-4 md:w-2/5" x-data="{ id: '{{ $order->id }}', copied: false }">
                    <p>Delivery address</p>
                    <div class="text-sm text-fuchsia-400">
                        <p>{{ $order->address }}</p>
                        <p>{{ $order->postcode }}</p>
                        <p>{{ $order->state }}</p>
                    </div>
                    @if ($order->status != 4 && $order->status == 3)
                        <x-jet-button type="button" class=" mt-6"
                            wire:click="received('{{ $order->id }}')">
                            Order Received
                        </x-jet-button>
                        <a href="https://www.jtexpress.my/tracking/{!! $order->tracking_number !!}" target="_blank">
                            <x-jet-button-utility type="button" class="mt-2">
                                Track your order
                            </x-jet-button-utility>
                        </a>
                    @elseif($order->status == 4)
                        <p class="absolute p-2 font-bold uppercase text-lime-500 border-lime-500 bottom-6 border-y-2">
                            Order Completed</p>
                    @endif
                </div>
            </div>
            <div
                class="flex flex-col p-4 mt-4 space-y-5 transition shadow-md bg-black/30 rounded-xl hover:shadow-indigo-500/30">
                <p>Order Progress</p>
                <div class="relative">
                    <p class="absolute w-full h-2 rounded-full bg-neutral-800"></p>
                    @if ($order->status == 1)
                        <p
                            class="absolute w-[10%] h-2 bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500 rounded-full">
                        </p>
                    @elseif($order->status == 2)
                        <p
                            class="absolute h-2 bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500 rounded-full w-[40%]">
                        </p>
                    @elseif($order->status == 3)
                        <p
                            class="absolute w-[70%] h-2 bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500 rounded-full">
                        </p>
                    @elseif($order->status == 4)
                        <p
                            class="absolute w-full h-2 rounded-full bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500">
                        </p>
                    @endif
                </div>
                <div class="flex justify-between px-2">
                    <p>Order placed</p>
                    <p>Processing</p>
                    <p>Shipped</p>
                    <p>Delivered</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
