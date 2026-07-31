@section('title', 'Log in | HopeXito')

<x-guest-layout>
    <x-auth-modal initial-mode="login" :force-open="true" />
</x-guest-layout>
