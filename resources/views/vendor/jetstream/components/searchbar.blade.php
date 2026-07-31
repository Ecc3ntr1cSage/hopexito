<style>
    .gradient-border {
        --borderWidth: 3px;
        background: #1D1F20;
        position: relative;
    }

    .gradient-border:after {
        content: '';
        position: absolute;
        top: calc(-1 * var(--borderWidth));
        left: calc(-1 * var(--borderWidth));
        height: calc(100% + var(--borderWidth) * 2);
        width: calc(100% + var(--borderWidth) * 2);
        background: linear-gradient(60deg, #f43f5e, #ec4899, #d946ef, #a855f7, #8b5cf6, #6366f1, #3b82f6, #06b6d4);
        z-index: -1;
        animation: animatedgradient 3s ease alternate infinite;
        background-size: 300% 300%;
    }

    @keyframes animatedgradient {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }
</style>
<div class="relative w-full" x-data="{ open: false }">
    <div class="relative z-0 p-0.5 overflow-hidden rounded-lg gradient-border">
        <form action="{{ route('search') }}" class="relative z-50 flex rounded-md" method="GET">
            <x-jet-input type="text" class="block w-full" autocomplete="off" name="search" x-on:click="open = true"
                x-ref="searchInput" placeholder="Search for designs or users" />
            <button type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor"
                    class="absolute bottom-0 right-0 w-10 h-10 p-2 text-indigo-500 transition rounded-full cursor-pointer hover:bg-violet-500/40">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </button>
        </form>
    </div>
    <div x-cloak class="absolute z-40 flex justify-center -inset-x-16 inset-y-16" x-show="open" x-transition.duration.350ms
        x-on:click.away="open = false">
        <div class="absolute inset-0 z-0 p-1 overflow-hidden rounded-2xl gradient-border w-96 md:w-full h-fit lg:h-[500px]">
           <div class="w-full h-full p-4 bg-black rounded-xl">
            @livewire('searchbar')
           </div>
        </div>
    </div>
</div>
