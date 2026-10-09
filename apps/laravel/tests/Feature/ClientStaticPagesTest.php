<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientStaticPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_with_header_and_primary_cta(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSeeText('Kho xe');
        $response->assertSeeText('Ve chung toi');
        $response->assertSeeText('Di den kho xe');
    }

    public function test_contact_family_pages_render_header_and_form_shell(): void
    {
        $pages = [
            route('contact') => 'Lien He Showroom',
            route('finance') => 'Dang Ky Tu Van Tai Chinh',
            route('tradein') => 'Dang Ky Thu Cu Doi Moi',
        ];

        foreach ($pages as $url => $title) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertSeeText('Kho xe');
            $response->assertSeeText($title);
            $response->assertSeeText('Gui thong tin tu van');
        }
    }

    public function test_missing_route_uses_custom_404_page(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/trang-khong-ton-tai');

        $response->assertNotFound();
        $response->assertSeeText('Oops! Trang ban tim hien khong co san.');
        $response->assertSeeText('Ve trang chu');
    }
}
