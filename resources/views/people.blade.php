@section('title', $user->name . ' | HopeXito')
<x-app-layout>
    <div class="min-h-screen bg-neutral-900 px-4 py-10 text-white">
        <div class="mx-auto max-w-6xl">
            <div class="overflow-hidden rounded-3xl border border-indigo-500/50 bg-black/50">
                @if ($user->profile?->cover_image)
                    <img class="h-48 w-full object-cover" src="{{ asset('storage/cover-image/' . $user->profile->cover_image) }}" alt="">
                @else
                    <div class="h-48 bg-gradient-to-r from-indigo-900 via-fuchsia-900 to-rose-900"></div>
                @endif
                <div class="flex flex-wrap items-center gap-5 p-6">
                    <img class="h-20 w-20 rounded-full object-cover ring-2 ring-indigo-400" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                    <div><h1 class="text-3xl font-bold">{{ $user->name }}</h1><p class="text-sm text-gray-300">{{ $user->profile?->bio }}</p></div>
                    <div class="ml-auto text-right text-sm text-gray-300"><p>{{ $productsCount }} public designs</p><p>{{ $totalSold }} items sold</p></div>
                </div>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-4">
                @forelse ($products as $product)
                    <a href="{{ route('product.show', $product->slug) }}" class="rounded-xl border border-indigo-500/30 bg-white/5 p-2 hover:border-fuchsia-400">
                        <img class="aspect-square w-full rounded-lg object-cover" src="{{ $product->product_image }}" alt="{{ $product->title }}">
                        <p class="mt-2 truncate font-medium">{{ $product->title }}</p>
                        <p class="text-sm text-fuchsia-300">RM{{ number_format($product->price, 2) }}</p>
                        @if ($product->visibility === 'private')<span class="text-xs text-amber-300">Private</span>@endif
                    </a>
                @empty
                    <p class="col-span-full py-12 text-center text-gray-400">No products published yet.</p>
                @endforelse
            </div>
            <div class="mt-8">{{ $products->links('/vendor/pagination/tailwind') }}</div>
        </div>
    </div>
</x-app-layout>
