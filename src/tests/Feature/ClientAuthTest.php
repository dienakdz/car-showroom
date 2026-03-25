<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_guest_can_register_from_client_auth_page(): void
    {
        $response = $this->post(route('register'), [
            'form_mode' => 'register',
            'name' => 'Client Buyer',
            'email' => 'buyer@example.com',
            'phone' => '0901234567',
            'password' => 'secret123',
            'accept_privacy' => '1',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionMissing('success');
        $response->assertSessionHas('flasher::envelopes');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'buyer@example.com',
            'phone' => '0901234567',
        ]);
    }

    public function test_guest_can_login_from_client_auth_page(): void
    {
        $user = User::query()->create([
            'name' => 'Existing Buyer',
            'email' => 'login@example.com',
            'phone' => '0907654321',
            'password' => 'secret123',
        ]);

        $response = $this->post(route('login.attempt'), [
            'form_mode' => 'login',
            'identifier' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionMissing('success');
        $response->assertSessionHas('flasher::envelopes');
        $this->assertAuthenticatedAs($user);
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

    public function test_authenticated_user_can_update_profile_from_account_page(): void
    {
        $user = User::query()->create([
            'name' => 'Profile User',
            'email' => 'profile@example.com',
            'phone' => '0901111222',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($user)->post(route('account.profile.update'), [
            'form_mode' => 'account_profile',
            'name' => 'Profile Updated',
            'email' => 'profile.updated@example.com',
            'phone' => '0903333444',
        ]);

        $response->assertRedirect(route('account.show', ['tab' => 'account-profile']));
        $response->assertSessionMissing('success');
        $response->assertSessionHas('flasher::envelopes');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Profile Updated',
            'email' => 'profile.updated@example.com',
            'phone' => '0903333444',
        ]);
    }

    public function test_authenticated_user_can_change_password_from_account_page(): void
    {
        $user = User::query()->create([
            'name' => 'Password User',
            'email' => 'password@example.com',
            'phone' => '0905555666',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($user)->post(route('account.password.update'), [
            'form_mode' => 'account_password',
            'current_password' => 'secret123',
            'new_password' => 'newsecret123',
            'new_password_confirmation' => 'newsecret123',
        ]);

        $response->assertRedirect(route('account.show', ['tab' => 'account-profile']));
        $response->assertSessionMissing('success');
        $response->assertSessionHas('flasher::envelopes');
        $this->assertTrue(Hash::check('newsecret123', $user->refresh()->password));
    }
}
