<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClientTrimReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_submitting_a_review(): void
    {
        $fixture = $this->createTrimFixture();

        $response = $this->post(route('trim.reviews.store', ['trimSlug' => $fixture['trim_slug']]), [
            'rating' => 5,
            'comment' => 'Xe van hanh on dinh va rat dung nhu nhu cau gia dinh.',
        ]);

        $response->assertRedirect('/dang-nhap');
        $this->assertDatabaseCount('trim_reviews', 0);
    }

    public function test_authenticated_user_cannot_review_without_a_purchase(): void
    {
        $fixture = $this->createTrimFixture();
        $user = $this->createUser('buyer-no-sale@example.com', '0901111111');

        $response = $this->actingAs($user)->post(route('trim.reviews.store', ['trimSlug' => $fixture['trim_slug']]), [
            'rating' => 4,
            'comment' => 'Toi muon danh gia nhung thuc te chua mua xe nay.',
        ]);

        $response->assertRedirect(route('trim.show', ['trimSlug' => $fixture['trim_slug']]));
        $response->assertSessionHasErrors('review');
        $this->assertDatabaseCount('trim_reviews', 0);
    }

    public function test_buyer_can_submit_a_trim_review(): void
    {
        $fixture = $this->createTrimFixture();
        $buyer = $this->createUser('buyer@example.com', '0902222222');
        $staff = $this->createUser('staff@example.com', '0903333333');

        DB::table('sales')->insert([
            'car_unit_id' => $fixture['car_unit_id'],
            'buyer_user_id' => $buyer->id,
            'created_by' => $staff->id,
            'sold_price' => 720000000,
            'sold_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($buyer)->post(route('trim.reviews.store', ['trimSlug' => $fixture['trim_slug']]), [
            'rating' => 5,
            'comment' => 'Da mua va su dung mot thoi gian, trai nghiem rat tot va dang tien.',
        ]);

        $response->assertRedirect(route('trim.show', ['trimSlug' => $fixture['trim_slug']]));
        $this->assertDatabaseHas('trim_reviews', [
            'trim_id' => $fixture['trim_id'],
            'user_id' => $buyer->id,
            'rating' => 5,
            'status' => 'pending',
        ]);
    }

    private function createTrimFixture(): array
    {
        $makeId = DB::table('makes')->insertGetId([
            'name' => 'Honda',
            'slug' => 'honda',
            'logo_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $modelId = DB::table('models')->insertGetId([
            'make_id' => $makeId,
            'name' => 'Civic',
            'slug' => 'civic',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $trimId = DB::table('trims')->insertGetId([
            'model_id' => $modelId,
            'name' => 'RS',
            'slug' => 'civic-rs',
            'year_from' => 2024,
            'year_to' => null,
            'msrp' => 720000000,
            'description' => 'Phien ban thu nghiem cho review middleware.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $carUnitId = DB::table('car_units')->insertGetId([
            'trim_id' => $trimId,
            'condition' => 'used',
            'vin' => 'VIN-TEST-0001',
            'stock_code' => 'TEST-CIVIC-001',
            'year' => 2024,
            'mileage' => 12000,
            'body_type_id' => null,
            'fuel_type_id' => null,
            'transmission_id' => null,
            'drivetrain_id' => null,
            'exterior_color_id' => null,
            'interior_color_id' => null,
            'price' => 720000000,
            'currency' => 'VND',
            'status' => 'sold',
            'hold_until' => null,
            'published_at' => now()->subDays(10),
            'sold_at' => now()->subDay(),
            'notes_internal' => null,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'trim_id' => $trimId,
            'trim_slug' => 'civic-rs',
            'car_unit_id' => $carUnitId,
        ];
    }

    private function createUser(string $email, string $phone): User
    {
        return User::query()->create([
            'name' => strtok($email, '@'),
            'email' => $email,
            'phone' => $phone,
            'password' => 'secret123',
        ]);
    }
}
