<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_client_auth_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSeeText('Dang nhap');
        $response->assertSeeText('Tao tai khoan');
    }

    public function test_authenticated_user_can_open_account_management_sections(): void
    {
        $user = User::query()->create([
            'name' => 'Account User',
            'email' => 'account@example.com',
            'phone' => '0908888777',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($user)->get(route('account.show'));

        $response->assertOk();
        $response->assertSeeText('Quan ly tai khoan');
        $response->assertSeeText('Thong tin ca nhan');
        $response->assertSeeText('Doi mat khau');
        $response->assertSeeText('Lich hen cua toi');
        $response->assertSeeText('Yeu cau cua toi');
        $response->assertSeeText('Xe da mua');
        $response->assertSeeText('Danh gia cua toi');
    }

    public function test_authenticated_user_is_redirected_from_login_to_account_page(): void
    {
        $user = User::query()->create([
            'name' => 'Redirect User',
            'email' => 'redirect@example.com',
            'phone' => '0901111999',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('account.show'));
    }
}
