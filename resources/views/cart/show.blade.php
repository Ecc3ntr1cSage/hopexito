@section('title', 'Checkout | HopeXito')
<x-app-layout>
    <x-jet-session-message />
    <div class="relative w-full mx-auto text-white bg-zinc-900">
        <div class="grid min-h-screen grid-cols-10" x->
            <div class="px-4 py-6 lg:pb-56 col-span-full lg:col-span-6">
                <div class="w-full max-w-lg mx-auto">
                    <div class="flex justify-between">
                        <h1
                            class="relative text-3xl font-medium text-transparent bg-clip-text bg-gradient-to-r from-violet-500 to-orange-500">
                            Secure Checkout<span class="block w-10 h-1 mt-2 bg-rose-500 sm:w-20"></span></h1>
                        <a href="{{ route('cart.index') }}" class="">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-8 h-8 p-1 text-white transition duration-1000 ease-in-out rounded-full hover:rotate-180 hover:text-rose-500 hover:bg-rose-500/30">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>
                    <form method="POST" action="{{ route('billplz-store') }}" class="flex flex-col mt-2 space-y-4"
                        x-transition.duration.500ms x-data="{ radio: '' }">
                        @csrf
                        <div class="flex items-center justify-between">
                            <div class="flex flex-row items-center space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-indigo-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                <h2 class="font-medium text-indigo-400">Delivery Address</h2>
                            </div>
                        </div>
                        @if (Auth::check())
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="email" value="{{ __('Email') }}" />
                                <x-jet-input id="email" type="email" name="email" class="block w-full mt-1"
                                    value="{{ Auth::user()->email }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="name" value="{{ __('Name') }}" />
                                <x-jet-input id="name" type="text" name="name" class="block w-full mt-1"
                                    value="{{ Auth::user()->name }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="phone" value="{{ __('Phone Number') }}" />
                                <x-jet-input id="phone" type="text" name="phone" class="block w-full mt-1"
                                    value="+60{{ Auth::user()->phone }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="address" value="{{ __('Address') }}" />
                                <x-jet-input id="address" type="text" name="address" class="block w-full mt-1"
                                    value="{{ Auth::user()->address }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="postcode" value="{{ __('Postcode') }}" />
                                <x-jet-input id="postcode" type="text" name="postcode" class="block w-full mt-1"
                                    value="{{ Auth::user()->postcode }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="state" value="{{ __('State') }}" />
                                <select name="state" disabled
                                    class="block w-full p-2.5 bg-neutral-800 border border-neutral-500 rounded-md focus:ring-indigo-500">
                                    <option>{{ $state }}</option>
                                </select>
                            </div>
                        @else
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="email" value="{{ __('Email') }}" />
                                <x-jet-input id="email" type="email" name="email" class="block w-full mt-1"
                                    value="{{ $details['email'] }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="name" value="{{ __('Name') }}" />
                                <x-jet-input id="name" type="text" name="name" class="block w-full mt-1"
                                    value="{{ $details['name'] }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="phone" value="{{ __('Phone Number') }}" />
                                <x-jet-input id="phone" type="text" name="phone" class="block w-full mt-1"
                                    value="+60{{ $details['phone'] }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="address" value="{{ __('Address') }}" />
                                <x-jet-input id="address" type="text" name="address" class="block w-full mt-1"
                                    value="{{ $details['address'] }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="postcode" value="{{ __('Postcode') }}" />
                                <x-jet-input id="postcode" type="text" name="postcode" class="block w-full mt-1"
                                    value="{{ $details['postcode'] }}" disabled />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="state" value="{{ __('State') }}" />
                                <select name="state" disabled
                                    class="block w-full p-2.5 bg-neutral-800 border border-neutral-500 rounded-md focus:ring-indigo-500">
                                    <option>{{ $state }}</option>
                                </select>
                            </div>
                        @endif
                        <div class="flex flex-row items-center space-x-3 text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            <h2 class="font-medium">Demo Payment</h2>
                        </div>
                        @error('radio')
                            <p class="mt-1 text-rose-500">Please choose a bank</p>
                        @enderror
                        <div class="grid w-full max-w-screen-sm grid-cols-3 gap-3">
                            {{-- <div>
                                <input class="hidden" id="maybank" type="radio" name="radio" value="BP-FKR01"
                                    x-on:click="radio = BP-FKR01" x-model="radio">
                                <label class="flex flex-col p-4 border-2 rounded-md cursor-pointer bg-neutral-900"
                                    :class="radio == 'BP-FKR01' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-500'"
                                    for="maybank">
                                    <img src="image\fpx-logo\maybank.png" class="h-4 md:h-8" />
                                </label>
                            </div> --}}
                            <div>
                                <input class="hidden" id="maybank" type="radio" name="radio" value="MB2U0227"
                                    x-on:click="radio = MB2U0227" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'MB2U0227' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="maybank">
                                    <img src="image\fpx-logo\maybank.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="demo" type="radio" name="radio" value="BIMB0340"
                                    x-on:click="radio = BIMB0340" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'BIMB0340' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="demo">
                                    <img src="image\fpx-logo\bank-islam.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                          <div>
                                <input class="hidden" id="cimb" type="radio" name="radio" value="BCBB0235"
                                    x-on:click="radio = BCBB0235" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'BCBB0235' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="cimb">
                                    <img src="image\fpx-logo\cimb.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="rhb" type="radio" name="radio" value="RHB0218"
                                    x-on:click="radio = RHB0218" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'RHB0218' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="rhb">
                                    <img src="image\fpx-logo\rhb.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="ambank" type="radio" name="radio" value="AMBB0209"
                                    x-on:click="radio = AMBB0209" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'AMBB0209' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="ambank">
                                    <img src="image\fpx-logo\ambank.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="affin" type="radio" name="radio" value="ABB0233"
                                    x-on:click="radio = ABB0233" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'ABB0233' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="affin">
                                    <img src="image\fpx-logo\affin.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="bsn" type="radio" name="radio" value="BSN0601"
                                    x-on:click="radio = BSN0601" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'BSN0601' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="bsn">
                                    <img src="image\fpx-logo\bsn.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="bank-rakyat" type="radio" name="radio"
                                    value="BKRM0602" x-on:click="radio = BKRM0602" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'BKRM0602' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="bank-rakyat">
                                    <img src="image\fpx-logo\bank-rakyat.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="uob" type="radio" name="radio" value="UOB0226"
                                    x-on:click="radio = UOB0226" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'UOB0226' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="uob">
                                    <img src="image\fpx-logo\uob.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="hlb" type="radio" name="radio" value="HLB0224"
                                    x-on:click="radio = HLB0224" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'HLB0224' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="hlb">
                                    <img src="image\fpx-logo\hlb.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="pb" type="radio" name="radio" value="PBB0233"
                                    x-on:click="radio = PBB0233" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'PBB0233' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="pb">
                                    <img src="image\fpx-logo\pb.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="scb" type="radio" name="radio" value="SCB0216"
                                    x-on:click="radio = SCB0216" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'SCB0216' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="scb">
                                    <img src="image\fpx-logo\scb.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="ocbc" type="radio" name="radio" value="OCBC0229"
                                    x-on:click="radio = OCBC0229" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'OCBC0229' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="ocbc">
                                    <img src="image\fpx-logo\ocbc.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="hsbc" type="radio" name="radio" value="HSBC0223"
                                    x-on:click="radio = HSBC0223" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'HSBC0223' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="hsbc">
                                    <img src="image\fpx-logo\hsbc.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                            <div>
                                <input class="hidden" id="alliancebank" type="radio" name="radio"
                                    value="ABMB0212" x-on:click="radio = ABMB0212" x-model="radio">
                                <label class="flex flex-col p-4 bg-transparent border-2 rounded-md cursor-pointer"
                                    :class="radio == 'ABMB0212' ? 'ring ring-indigo-500 border-indigo-500' :
                                        'border-neutral-700'"
                                    for="alliancebank">
                                    <img src="image\fpx-logo\alliancebank.png" class="h-4 md:h-8" />
                                </label>
                            </div>
                        </div>
                        <p class="pt-2 text-xs">By placing this order you agree to the
                            <a href="{{ route('terms.show') }}"
                                class="text-indigo-400 underline whitespace-nowrap hover:text-indigo-500">Terms of
                                Services</a>
                        </p>
                        <x-jet-button>
                            <span class="p-2 mx-auto">Pay Now</span>
                        </x-jet-button>
                    </form>
                </div>
            </div>
            <div class="relative flex flex-col p-2 md:p-6 col-span-full lg:col-span-4">
                <div>
                    <img src="image\checkout.jpg" alt=""
                        class="absolute inset-0 object-cover w-full h-full" />
                    <div
                        class="absolute inset-0 w-full h-full opacity-95 bg-gradient-to-tr from-rose-800 to-indigo-800">
                    </div>
                </div>
                <div class="relative font-mono">
                    <ul class="space-y-3">
                        @foreach ($cart as $cart)
                            <li
                                class="flex justify-between p-4 transition hover:skew-y-1 rounded-xl hover:shadow-lg hover:shadow-orange-500/20 bg-neutral-900">
                                <div class="inline-flex">
                                    @if(Auth::check())
                                    <img class="max-h-24" src="{{ $cart->cartProduct->product_image }}"
                                    alt="" />
                                    @else
                                    <img class="max-h-24" src="{{ $cart->options['product_image'] }}"
                                    alt="" />
                                    @endif
                                    <div class="ml-5">
                                        @if (Auth::check())
                                            <p class="text-base font-semibold text-white">{{ $cart->title }}</p>
                                            <p class="text-white uppercase text-opacity-80">
                                                {{ $cart->size }}
                                                / {{ $cart->color }}</p>
                                        @else
                                            <p class="text-base font-semibold text-white">{{ $cart->name }}</p>
                                            <p class="text-white uppercase text-opacity-80">
                                                {{ $cart->options['size'] }}
                                                / {{ $cart->options['color'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if (Auth::check())
                                    <p class="text-base font-semibold text-white">
                                        RM{{ number_format($cart->price, 2) }} x {{ $cart->quantity }}</p>
                                @else
                                    <p class="text-base font-semibold text-white">
                                        RM{{ number_format($cart->price, 2) }} x {{ $cart->qty }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <div class="my-5 h-0.5 w-full bg-white bg-opacity-30"></div>
                    <div class="space-y-3">
                        @if (Auth::check())
                            <x-jet-button-custom onclick="window.location.href='{{ route('profile.show') }}'"
                                class="w-full">
                                Change delivery address
                            </x-jet-button-custom>
                        @endif
                        <p class="flex justify-between text-white"><span>Shipping (JNT
                                Express)</span>
                            <span>RM {{ number_format($delivery, 2) }}</span>
                        </p>
                        <p class="flex justify-between text-white">
                            <span>Subtotal:</span><span> RM {{ number_format($subtotal, 2) }}</span>
                        </p>
                        <p class="flex justify-between text-lg font-bold tracking-wide text-lime-300">
                            <span>Total Price:</span><span>
                                RM {{ number_format($total, 2) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
