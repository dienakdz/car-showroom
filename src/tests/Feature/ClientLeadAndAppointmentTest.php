<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClientLeadAndAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_client_lead_stores_user_id_and_trim_context(): void
    {
        $fixture = $this->createInventoryFixture();
        $user = $this->createUser('lead-client@example.com', '0904444444');

        $response = $this->from(route('trim.show', ['trimSlug' => $fixture['trim_slug']]))
            ->actingAs($user)
            ->post(route('lead.store'), [
                'source' => 'trim_page',
                'name' => 'Lead Client',
                'phone' => '0904444444',
                'email' => 'lead-client@example.com',
                'message' => 'Toi muon nhan tu van cho phien ban nay.',
                'trim_id' => $fixture['trim_id'],
            ]);

        $response->assertRedirect(route('trim.show', ['trimSlug' => $fixture['trim_slug']]));

        $this->assertDatabaseHas('leads', [
            'user_id' => $user->id,
            'trim_id' => $fixture['trim_id'],
            'source' => 'trim_page',
            'status' => 'new',
        ]);
    }

    public function test_non_contact_lead_requires_car_or_trim_context(): void
    {
        $response = $this->from(route('finance'))->post(route('lead.store'), [
            'source' => 'finance',
            'name' => 'No Context',
            'phone' => '0905555555',
            'email' => 'nocontext@example.com',
            'message' => 'Cho toi biet thu tuc vay.',
        ]);

        $response->assertRedirect(route('finance'));
        $response->assertSessionHasErrors('trim_id');
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_booking_creates_linked_lead_and_appointment(): void
    {
        $fixture = $this->createInventoryFixture();
        $user = $this->createUser('booking-client@example.com', '0906666666');

        $response = $this->from(route('car.show', ['stockCode' => $fixture['stock_code']]))
            ->actingAs($user)
            ->post(route('appointments.store'), [
                'source' => 'unit_detail',
                'name' => 'Booking Client',
                'phone' => '0906666666',
                'email' => 'booking-client@example.com',
                'message' => 'Toi muon dat lich xem xe vao cuoi tuan.',
                'car_unit_id' => $fixture['car_unit_id'],
                'trim_id' => $fixture['trim_id'],
                'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect(route('car.show', ['stockCode' => $fixture['stock_code']]));

        $leadId = DB::table('leads')
            ->where('user_id', $user->id)
            ->where('car_unit_id', $fixture['car_unit_id'])
            ->where('trim_id', $fixture['trim_id'])
            ->value('id');

        $this->assertNotNull($leadId);
        $this->assertDatabaseHas('leads', [
            'id' => $leadId,
            'source' => 'unit_detail',
            'status' => 'booked',
        ]);
        $this->assertDatabaseHas('appointments', [
            'user_id' => $user->id,
            'car_unit_id' => $fixture['car_unit_id'],
            'trim_id' => $fixture['trim_id'],
            'lead_id' => $leadId,
            'status' => 'pending',
        ]);
    }

    public function test_booking_rejects_a_past_schedule(): void
    {
        $fixture = $this->createInventoryFixture();

        $response = $this->from(route('car.show', ['stockCode' => $fixture['stock_code']]))
            ->post(route('appointments.store'), [
                'source' => 'unit_detail',
                'name' => 'Late Client',
                'phone' => '0907777777',
                'email' => 'late@example.com',
                'message' => 'Hen toi hom qua.',
                'car_unit_id' => $fixture['car_unit_id'],
                'trim_id' => $fixture['trim_id'],
                'scheduled_at' => now()->subHour()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect(route('car.show', ['stockCode' => $fixture['stock_code']]));
        $response->assertSessionHasErrors('scheduled_at');
        $this->assertDatabaseCount('leads', 0);
        $this->assertDatabaseCount('appointments', 0);
    }

    private function createInventoryFixture(): array
    {
        $makeId = DB::table('makes')->insertGetId([
            'name' => 'Toyota',
            'slug' => 'toyota',
            'logo_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $modelId = DB::table('models')->insertGetId([
            'make_id' => $makeId,
            'name' => 'Corolla Cross',
            'slug' => 'corolla-cross',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $trimId = DB::table('trims')->insertGetId([
            'model_id' => $modelId,
            'name' => 'Hybrid',
            'slug' => 'corolla-cross-hybrid',
            'year_from' => 2024,
            'year_to' => null,
            'msrp' => 905000000,
            'description' => 'Trim dung de test lead va booking.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $carUnitId = DB::table('car_units')->insertGetId([
            'trim_id' => $trimId,
            'condition' => 'used',
            'vin' => 'VIN-LEAD-0001',
            'stock_code' => 'LEAD-BOOK-001',
            'year' => 2024,
            'mileage' => 12000,
            'body_type_id' => null,
            'fuel_type_id' => null,
            'transmission_id' => null,
            'drivetrain_id' => null,
            'exterior_color_id' => null,
            'interior_color_id' => null,
            'price' => 905000000,
            'currency' => 'VND',
            'status' => 'available',
            'hold_until' => null,
            'published_at' => now()->subDay(),
            'sold_at' => null,
            'notes_internal' => null,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'trim_id' => $trimId,
            'trim_slug' => 'corolla-cross-hybrid',
            'car_unit_id' => $carUnitId,
            'stock_code' => 'LEAD-BOOK-001',
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
