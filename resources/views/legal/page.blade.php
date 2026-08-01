@php
    $otherRoute = $otherRoute ?? route('policy.show');
@endphp

<div class="legal-page">
    <header class="legal-hero">
        <div class="legal-container">
            <div class="legal-topline">
                <span>HopeXito / legal desk</span>
                <span>Effective 01 August 2026</span>
            </div>

            <div class="legal-hero-grid">
                <div class="legal-hero-copy">
                    <span class="legal-eyebrow">{{ $eyebrow }}</span>
                    <h1>{{ $headline }}<br><em>{{ $accent }}</em></h1>
                    <p>{{ $lede }}</p>
                </div>

                <aside class="legal-summary">
                    <span class="legal-summary-label">At a glance</span>
                    <strong>{{ $summaryTitle }}</strong>
                    <p>{{ $summary }}</p>
                    <div class="legal-summary-rule"></div>
                    <a href="{{ $otherRoute }}">{{ $otherLabel }} <span aria-hidden="true">&nearr;</span></a>
                </aside>
            </div>
        </div>
    </header>

    <main class="legal-container legal-body">
        <aside class="legal-rail">
            <div class="legal-rail-card">
                <span>Document</span>
                <strong>{{ $documentType }}</strong>
                <p>Last revised<br>01 August 2026</p>
            </div>
            <a class="legal-rail-link" href="mailto:support@hopexito.com">Questions? <span>support@hopexito.com</span></a>
        </aside>

        <article class="legal-document">
            {!! $content !!}
        </article>
    </main>
</div>
