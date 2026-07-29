 <nav x-data="{ open: false }" class="justify-between text-gray-400 bg-zinc-900">
     <!-- Primary Navigation Menu -->
     <div class="block px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
         <div class="flex justify-between h-20">
             <!-- Logo -->
             <div class="flex items-center shrink-0">
                 <x-jet-application-logo class="block w-auto" />
             </div>
             <div class="items-center hidden w-1/2 md:flex">
                 <x-jet-searchbar></x-jet-searchbar>
             </div>
             <!-- Settings Dropdown -->
             <div class="flex-wrap items-center justify-center hidden text-base xl:flex">
                 @if (Route::has('login'))
                     @auth
                         @if (Auth::user()->role_id == 2 || Auth::user()->role_id == 3)
                             <x-jet-dropdown align="right" width="48">
                                 <x-slot name="trigger">
                                     @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                         <span class="inline-flex px-4 py-2 rounded-md prose-cyan">
                                             <button type="button" class="">
                                                 <img class="object-cover w-10 h-10 rounded-full"
                                                     src="{{ Auth::user()->profile_photo_url }}"
                                                     alt="{{ Auth::user()->name }}" />
                                             </button>
                                         </span>
                                     @else
                                         <span class="inline-flex rounded-md">
                                             <button type="button" class="">
                                                 <span><svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8"
                                                         fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                         stroke-width="2">
                                                         <path stroke-linecap="round" stroke-linejoin="round"
                                                             d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                     </svg></span>
                                             </button>
                                         </span>
                                     @endif
                                 </x-slot>
                                 <x-slot name="content">
                                     <!-- Account Management -->
                                     <div
                                         class="block px-4 py-2 text-lg text-center text-white rounded-t-lg bg-neutral-800">
                                         {{ Auth::user()->name }}
                                     </div>
                                     <div class="p-1 text-sm rounded-b-lg bg-neutral-800">
                                         @if (Auth::user()->role_id == 2)
                                             <x-jet-dropdown-link href="{{ route('dashboard') }}">
                                                 {{ __('Dashboard') }}
                                             </x-jet-dropdown-link>
                                             <x-jet-dropdown-link href="{{ route('people', Auth::user()->name) }}">
                                                 {{ __('Your Profile') }}
                                             </x-jet-dropdown-link>
                                             <x-jet-dropdown-link href="{{ route('product.manage') }}">
                                                 {{ __('Manage Products') }}
                                             </x-jet-dropdown-link>
                                             <x-jet-dropdown-link href="{{ route('product.sales') }}">
                                                {{ __('Sales History') }}  
                                                {{-- <span class="px-2 ml-2 bg-indigo-500 rounded-md">New</span> --}}
                                            </x-jet-dropdown-link>
                                             <x-jet-button type="button"
                                                 onclick="location.href='{{ route('product.create') }}'"
                                                 class="w-60 px-8 py-2.5 mx-1 mb-3 text-white rounded-full bg-rose-500 ">
                                                 <span class="mx-auto">Add new product</span>
                                             </x-jet-button>
                                             <div class="border-t border-indigo-500 border-1"></div>
                                             <x-jet-dropdown-link href="{{ route('order.index') }}">
                                                 {{ __('Order History') }}
                                             </x-jet-dropdown-link>
                                             <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                                 {{ __('Account Settings') }}
                                             </x-jet-dropdown-link>
                                         @elseif(Auth::user()->role_id == 3)
                                             <x-jet-dropdown-link href="{{ route('order.index') }}">
                                                 {{ __('Order History') }}
                                             </x-jet-dropdown-link>
                                             <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                                 {{ __('Account Settings') }}
                                             </x-jet-dropdown-link>
                                             {{-- <x-jet-button-utility type="button"
                                                onclick="location.href='{{ route('product.index') }}'"
                                                class="w-56 px-8 py-2.5 mx-3 mb-3 text-white rounded-full bg-indigo-500 ">
                                                <span class="mx-auto">Buy For You</span>
                                             </x-jet-button-utility> --}}
                                             <x-jet-button type="button"
                                                 onclick="location.href='{{ route('sellyourart') }}'"
                                                 class="w-56 px-8 py-2.5 mx-3 mb-3 text-white rounded-full bg-rose-500 ">
                                                 <span class="mx-auto">Sell your art</span>
                                             </x-jet-button>

                          @endif
                          @if (Auth::user()->role_id == 2 || Auth::user()->role_id == 3)
                          <div class="border-t border-indigo-500 border-1"></div>
                          @endif
                                         <form method="POST" action="{{ route('logout') }}" x-data>
                                             @csrf
                                             <x-jet-dropdown-link href="{{ route('logout') }}"
                                                 @click.prevent="$root.submit();" class="text-rose-500">
                                                 {{ __('Log Out') }}
                                             </x-jet-dropdown-link>
                                         </form>
                                     </div>
                                 </x-slot>
                             </x-jet-dropdown>
                              @livewire('cart.cart-counter')
                          @endif
                     @else
                         <x-jet-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                             Login
                         </x-jet-nav-link>
                         <x-jet-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
                             Sign Up
                         </x-jet-nav-link>
                         <div class="relative group">
                             <div
                                 class="absolute -inset-1.5 group-hover:-inset-2.5 mx-6 bg-gradient-to-r from-rose-700 via-fuchsia-700 to-indigo-700 rounded-full blur opacity-50 group-hover:opacity-100 transition">
                             </div>
                             <x-jet-nav-link href="{{ route('sellyourart') }}"
                                 class="mx-6 border-none rounded-full bg-zinc-900">
                                 Sell Your Art
                             </x-jet-nav-link>
                         </div>
                         @livewire('cart.cart-counter')
                     @endauth
                 @endif
             </div>
             <!-- Hamburger -->
             <div class="flex items-center -mr-2 xl:hidden">
                 @livewire('cart.cart-counter')
                 <button @click="open = ! open"
                     class="inline-flex items-center justify-center p-2 text-gray-400 transition rounded-md hover:text-white hover:bg-indigo-500 focus:outline-none focus:bg-indigo-500">
                     <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                         <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                             stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             d="M4 6h16M4 12h16M4 18h16" />
                         <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                     </svg>
                 </button>
             </div>
         </div>
         <!-- Responsive Navigation Menu -->
         <div :class="{ 'block': open, 'hidden': !open }" class="hidden">
             <!-- Responsive Settings Options -->
             <div class="pt-4 pb-1 border-t-4 border-fuchsia-500">
                 @if (Route::has('login'))
                     @auth
                         <div class="flex items-center px-4">
                             @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                 <span class="inline-flex rounded-md">
                                     <button type="button"
                                         class="inline-flex items-center px-2 py-2 mx-4 text-sm font-medium leading-4 text-white transition bg-transparent rounded-full focus:ring focus: ring-rose-500 focus:text-rose-500">
                                         <img class="rounded-full w-14 h-14" src="{{ Auth::user()->profile_photo_url }}"
                                             alt="{{ Auth::user()->name }}" />
                                     </button>
                                 </span>
                             @else
                                 <span class="inline-flex rounded-md">
                                     <button type="button"
                                         class="inline-flex items-center px-2 py-2 mx-4 text-sm font-medium leading-4 text-white transition bg-transparent rounded-full hover:text-rose-500 hover:bg-slate-900 focus:ring focus: ring-rose-500 focus:text-rose-500">
                                         <span><svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                 <path stroke-linecap="round" stroke-linejoin="round"
                                                     d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                             </svg></span>
                                     </button>
                                 </span>
                             @endif
                             <div>
                                 <div class="text-base font-medium">{{ Auth::user()->name }}</div>
                                 <div class="text-sm font-medium">{{ Auth::user()->email }}</div>
                             </div>
                         </div>

                         <div class="mt-3 space-y-1">
                             <!-- Account Management -->
                             @if (Auth::user()->role_id == 2)
                                 <x-jet-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                                     {{ __('Dashboard') }}
                                 </x-jet-responsive-nav-link>
                                 <x-jet-responsive-nav-link href="{{ route('people', Auth::user()->name) }}"
                                     :active="request()->routeIs('people', Auth::user()->name)">
                                     {{ __('Your Profile') }}
                                 </x-jet-responsive-nav-link>
                                 <x-jet-responsive-nav-link href="{{ route('product.manage') }}" :active="request()->routeIs('product.manage')">
                                     {{ __('Manage Products') }}
                                 </x-jet-responsive-nav-link>
                                 <x-jet-responsive-nav-link href="{{ route('product.sales') }}" :active="request()->routeIs('product.sales')">
                                    {{ __('Sales History') }}
                                  
                                </x-jet-responsive-nav-link>
                                 <x-jet-section-border />
                             @endif
                             <x-jet-responsive-nav-link href="{{ route('order.index') }}" :active="request()->routeIs('order.index')">
                                 {{ __('Order History') }}
                             </x-jet-responsive-nav-link>
                             <x-jet-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                                 {{ __('Account Settings') }}
                             </x-jet-responsive-nav-link>
                             @if (Auth::user()->role_id == 3)
                             <x-jet-responsive-nav-link href="{{ route('sellyourart') }}" :active="request()->routeIs('sellyourart')">
                                {{ __('Sell Your Art') }}
                            </x-jet-responsive-nav-link>
                            @endif
                              
                             @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                 <x-jet-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                                     {{ __('API Tokens') }}
                                 </x-jet-responsive-nav-link>
                             @endif
                             <div class="border-t border-indigo-500 border-1"></div>
                             <!-- Authentication -->
                             <form method="POST" action="{{ route('logout') }}" x-data>
                                 @csrf
                                 <x-jet-responsive-nav-link href="{{ route('logout') }}"
                                     @click.prevent="$root.submit();">
                                     {{ __('Log Out') }}
                                 </x-jet-responsive-nav-link>
                             </form>
                         @else
                             <x-jet-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                                 {{ __('Login') }}
                             </x-jet-responsive-nav-link>
                             <x-jet-responsive-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
                                 {{ __('Sign Up') }}
                             </x-jet-responsive-nav-link>
                             <x-jet-responsive-nav-link href="{{ route('sellyourart') }}" :active="request()->routeIs('sellyourart')">
                                 {{ __('Sell Your Art') }}
                             </x-jet-responsive-nav-link>
                         @endauth
                 @endif
             </div>
         </div>
     </div>
     <div class="max-w-xl px-4 mx-auto mt-2 mb-8 md:hidden sm:mt-0">
        <x-jet-searchbar></x-jet-searchbar>
     </div>
 </nav>
