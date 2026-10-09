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
        $response->assertSeeText('Đăng nhập quản trị');
    }

    public function test_guest_is_redirected_to_admin_login_when_opening_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $this->seed(UsersAndRbacSeeder::class);

        $customer = User::query()->where('email', 'john@example.com')->firstOrFail();

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_user_can_view_modern_dashboard(): void
    {
        $this->seed(UsersAndRbacSeeder::class);

        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Tổng xe trong kho');
        $response->assertSeeText('Lead mới tiếp nhận');
        $response->assertSeeText('Lịch hẹn cần xử lý');
        $response->assertSeeText('Giao dịch tháng này');
        $response->assertSee('admin-dash-stacked-bar');
        $response->assertSee('admin-lead-chart');
    }
}
