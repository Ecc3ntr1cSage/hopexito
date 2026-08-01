@section('title', 'Privacy Notice | HopeXito')
<x-app-layout>
    @include('legal.page', [
        'eyebrow' => 'Privacy notice / 01',
        'headline' => 'Privacy',
        'accent' => 'with intent.',
        'lede' => 'A clear account of what HopeXito collects, why it is needed, and the choices you have over your information.',
        'summaryTitle' => 'Your data, plainly.',
        'summary' => 'We collect what helps the marketplace work, keep public work separate from private delivery details, and do not sell personal data.',
        'documentType' => 'Privacy notice',
        'otherRoute' => route('terms.show'),
        'otherLabel' => 'Read the Terms of Service',
        'content' => $policy,
    ])
</x-app-layout>
