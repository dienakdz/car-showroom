<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\UsersAndRbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_admin_login_page(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertSeeText('Dang nhap quan tri');
    }

    public function test_guest_is_redirected_to_admin_login_when_opening_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_and_open_dashboard(): void
    {
        $this->seed(UsersAndRbacSeeder::class);

        $response = $this->post(route('admin.login.attempt'), [
            'identifier' => 'admin@showroom.test',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        $dashboard = $this->get(route('admin.dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSeeText('Dashboard');
        $dashboard->assertSeeText('Lead trend 6 thang gan day');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $this->seed(UsersAndRbacSeeder::class);

        $customer = User::query()->where('email', 'john@example.com')->firstOrFail();

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }
}
