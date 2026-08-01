@inject('carbon', 'Carbon\Carbon')
@section('title', 'Sales History | HopeXito')
<div class="max-w-3xl min-h-screen mx-auto mt-8 mb-32 text-white" x-data="{ nav: 1 }">
    <x-jet-session-message />
    <h1
        class="px-2 text-2xl font-bold text-transparent md:px-0 bg-clip-text bg-gradient-to-r from-red-400 to-indigo-800">
        Sales History</h1>
    <p class="px-2 my-2 mb-8 md:px-0">Gain valuable insights into your sales history, revenue, and track the progress of
        your orders with their status.</p>
    <div class="mb-12">
        <div class="flex flex-col items-center justify-between px-2 mb-4 font-mono text-md md:flex-row">
            <p>Total products sold: <span class="text-indigo-400 uppercase">{{ $totalItem }}</span>
            </p>
            <p>Total commission: <span class="text-indigo-400">RM{{ number_format($totalCommission, 2) }}</span>
            </p>
        </div>
    </div>
    <div class="grid-cols-1 space-y-6">
        @foreach ($productOrders->sortByDesc('created_at') as $item)
            <div class="flex flex-col space-x-4 md:flex-row">
                <div class="relative mx-auto md:mx-0 w-60 h-60">
                    <img class="transition rounded-lg ring ring-indigo-700" src="{{ $item->product->product_card_image }}"
                        alt="" />
                </div>
                <div class="flex flex-col gap-1 py-2 mt-4 text-center w-96 md:mt-0 md:text-left">
                    @if (Str::endsWith($item->order->name, '(G)'))
                        <p class="px-2 py-0.5 bg-indigo-600 rounded-lg w-fit mx-auto md:mx-0">Purchased by
                            {{ Str::replaceLast('(G)', '', $item->order->name) }}
                        </p>
                    @else
                        <p class="px-2 py-0.5 bg-indigo-600 rounded-lg w-fit mx-auto md:mx-0">Purchased by
                            {{ $item->order->name }}</p>
                    @endif
                    <p class="mt-2 text-indigo-400">{{ $item->title }}</p>
                    <p class=uppercase">{{ $item->size }} / {{ $item->color }}
                    </p>
                    <p>Qty: {{ $item->quantity }} </p>
                    <p>Price: <span
                            class="tracking-wider text-fuchsia-400">RM{{ number_format($item->price, 2) }}</span>
                    </p>
                    <p>Your income: <span class="text-lime-400">RM{{ number_format($item->price * 0.15, 2) }}</span>
                    </p>
                    <p>Order created at: <span
                            class="text-fuchsia-400">{{ $carbon::parse($item->order->paid_at)->format('F d, Y') }}</span>
                    </p>
                </div>
                <div class="flex flex-col py-2">
                    <p class="px-2 py-0.5 bg-violet-600 rounded-lg mb-2 w-fit mx-auto md:mx-0">Status</p>
                    <div class="text-center md:text-left">
                        @if ($item->order->status == 1)
                            <p class="text-red-400">Order Placed</p>
                        @elseif($item->order->status == 2)
                            <p class="text-amber-400">Order Processing</p>
                        @elseif($item->order->status == 3)
                            <p class="text-lime-400">Order Shipped</p>
                        @elseif($item->order->status == 4)
                            <p class="text-green-400">Order Delivered</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
