@section('title', 'Terms of Service | HopeXito')
<x-app-layout>
    @include('legal.page', [
        'eyebrow' => 'Terms of service / 02',
        'headline' => 'Use it',
        'accent' => 'with care.',
        'lede' => 'The shared rules for using HopeXito, publishing work, and taking part in the marketplace.',
        'summaryTitle' => 'Make good work.',
        'summary' => 'Keep your account secure, upload work you have the right to use, and treat the people and ideas around you with care.',
        'documentType' => 'Terms of service',
        'otherRoute' => route('policy.show'),
        'otherLabel' => 'Read the Privacy Notice',
        'content' => $terms,
    ])
</x-app-layout>
