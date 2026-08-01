<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_privacy_notice_is_available_and_reflects_the_current_product(): void
    {
        $this->get(route('policy.show'))
            ->assertOk()
            ->assertSee('Privacy')
            ->assertSee('What this notice covers')
            ->assertSee('Personal Data Protection Act 2010')
            ->assertSee('support@hopexito.com')
            ->assertSee('At a glance');
    }

    public function test_terms_page_is_available_and_reflects_the_current_product(): void
    {
        $this->get(route('terms.show'))
            ->assertOk()
            ->assertSee('Use it')
            ->assertSee('The agreement')
            ->assertSee('payment simulation')
            ->assertSee('Governing law')
            ->assertSee('Malaysia');
    }
}
