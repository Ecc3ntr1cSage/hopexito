@section('title', 'Dashboard | HopeXito')
<x-app-layout>
    <x-jet-session-message />
    <div class="max-w-6xl px-4 py-12 mx-auto text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div><p class="text-sm uppercase tracking-[0.3em] text-indigo-300">Welcome back</p><h1 class="mt-2 text-4xl font-bold">{{ Auth::user()->name }}</h1></div>
            <a href="{{ route('product.create') }}" class="rounded-full bg-rose-500 px-6 py-3 font-semibold">Create product</a>
        </div>
        <div class="grid gap-5 mt-12 md:grid-cols-3">
            <a href="{{ route('product.create') }}" class="rounded-2xl border-2 border-indigo-500 bg-black/40 p-6 hover:border-fuchsia-400"><p class="text-indigo-300">Design</p><h2 class="mt-2 text-xl font-semibold">Create a product</h2><p class="mt-2 text-sm text-gray-400">Upload your artwork and choose Shirt, Sweatshirt, or Hoodie.</p></a>
            <a href="{{ route('product.manage') }}" class="rounded-2xl border-2 border-indigo-500 bg-black/40 p-6 hover:border-fuchsia-400"><p class="text-indigo-300">Manage</p><h2 class="mt-2 text-xl font-semibold">Your products</h2><p class="mt-2 text-sm text-gray-400">Edit, publish, archive, or organize your designs.</p></a>
            <a href="{{ route('people', Auth::user()->name) }}" class="rounded-2xl border-2 border-indigo-500 bg-black/40 p-6 hover:border-fuchsia-400"><p class="text-indigo-300">Share</p><h2 class="mt-2 text-xl font-semibold">Your profile</h2><p class="mt-2 text-sm text-gray-400">Showcase your public products and profile.</p></a>
        </div>
        <div class="mt-12">@livewire('wallet')</div>
    </div>
</x-app-layout>
