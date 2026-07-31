@section('title', 'Sign Up | HopeXito')
<x-app-layout>
    <x-jet-authentication-card>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="h-12"><span class="text-xl tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500">Join HopeXito</span></div>
            <div class="mt-4"><x-jet-label for="name" value="Username" /><x-jet-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" /></div>
            <div class="mt-4"><x-jet-label for="email" value="Email" /><x-jet-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required /></div>
            <div class="mt-4"><x-jet-label for="password" value="Password" /><x-jet-input id="password" class="block w-full mt-1" type="password" name="password" required autocomplete="new-password" /></div>
            <div class="mt-4"><x-jet-label for="password_confirmation" value="Confirm Password" /><x-jet-input id="password_confirmation" class="block w-full mt-1" type="password" name="password_confirmation" required autocomplete="new-password" /></div>
            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4"><label class="flex items-center"><x-jet-checkbox name="terms" id="terms" required /><span class="ml-2 text-sm text-gray-400">I agree to the terms and privacy policy.</span></label></div>
            @endif
            <div class="flex items-center justify-end mt-6"><a class="text-sm text-gray-400 underline" href="{{ route('login') }}">Already registered?</a><x-jet-button class="ml-4">Sign Up</x-jet-button></div>
        </form>
    </x-jet-authentication-card>
</x-app-layout>
