<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_about_page_loads(): void
    {
        $response = $this->get('/tentang');

        $response->assertOk();
        $response->assertViewIs('pages.about');
    }
}
