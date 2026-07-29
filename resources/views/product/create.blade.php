@section('title', 'Choose Product | HopeXito')
<x-app-layout>
    <x-jet-session-message />
    <div class="relative z-20 min-h-screen pt-8">
        <img src="../image/product-selection/product-selection.jpg"
            class="hidden lg:block absolute inset-0 object-cover w-full h-full bg-cover" />
        <div
            class="max-w-6xl mx-auto shadow-md backdrop-filter backdrop-blur-[6px] rounded-3xl bg-black/40 shadow-fuchsia-500">
            <div class="max-w-5xl pt-8 mx-auto sm:flex-row">
                <x-jet-title>
                    Products Selection
                </x-jet-title>
                <div x-cloak x-data="{ isMobile: window.innerWidth < 820 }" x-show="isMobile" class="p-2 mt-4 rounded-lg bg-indigo-700/50 min-w-lg">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <p class="">
                            It is not recommended to add products using your current screen size.
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 py-16 mx-auto text-white md:grid-cols-2">
                    <div class="block h-48 mx-2 group sm:mx-0">
                        <a href="{{ route('mockup.shirt') }}"
                            class=" relative flex flex-col items-center h-full rounded-3xl border-4 border-indigo-500 bg-black/40 p-8 transition group-hover:-translate-x-2 group-hover:-translate-y-2 group-hover:shadow-[8px_8px_0_0_#ec4899]">
                            <p class="my-3 text-lg text-indigo-400">Standard Tee</p>
                            <img src="../image/product-selection/tshirt.png"
                                class="w-16 h-16 p-2 bg-indigo-500 rounded-full" />
                        </a>
                    </div>
                    <div class="block h-48 mx-2 group sm:mx-0">
                        <a href="{{ route('mockup.oversized') }}"
                            class="relative flex flex-col items-center h-full rounded-3xl border-4 border-indigo-500 bg-black/40 p-8 transition group-hover:-translate-x-2 group-hover:-translate-y-2 group-hover:shadow-[8px_8px_0_0_#ec4899]">
                            <p class="my-3 text-lg text-indigo-400">Oversized Tee</p>
                            <img src="../image/product-selection/oversized.png"
                                class="w-16 h-16 p-2 bg-indigo-500 rounded-full" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isMobile = window.innerWidth < 820;
            Alpine.data('myComponent', () => ({
                isMobile: isMobile,
            }));
        });
    </script>
</x-app-layout>
