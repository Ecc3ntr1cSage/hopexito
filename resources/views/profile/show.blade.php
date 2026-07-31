@section('title', 'Account Details | HopeXito')
<x-app-layout>
    <x-jet-session-message />
    <div class="max-w-5xl px-4 py-12 mx-auto space-y-8">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')
            <x-jet-section-border />
        @endif
        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            @livewire('profile.update-password-form')
            <x-jet-section-border />
        @endif
        <div id="personalization">@livewire('cover-image-bio')</div>
        <x-jet-section-border />
        <div id="social-links">@livewire('social-links')</div>
        <x-jet-section-border />
        <div id="wallet">@livewire('wallet')</div>
        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <x-jet-section-border />
            @livewire('profile.delete-user-form')
        @endif
    </div>
</x-app-layout>
