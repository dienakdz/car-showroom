<?php

namespace Tests\Feature\Admin;

use App\Models\BodyType;
use App\Models\CarModel;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Make;
use App\Models\Trim;
use App\Models\User;
use Database\Seeders\UsersAndRbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInventoryAndSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_inventory_workflow_and_create_sale(): void
    {
        $this->seed(UsersAndRbacSeeder::class);

        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();

        $make = Make::query()->create([
            'name' => 'Mazda',
            'slug' => 'mazda',
        ]);
        $model = CarModel::query()->create([
            'make_id' => $make->id,
            'name' => 'CX-5',
            'slug' => 'cx-5',
        ]);
        $trim = Trim::query()->create([
            'model_id' => $model->id,
            'name' => 'Premium',
            'slug' => 'premium',
        ]);
        $bodyType = BodyType::query()->create([
            'name' => 'SUV',
            'slug' => 'suv',
        ]);

        $createResponse = $this->actingAs($admin)->post(route('admin.inventory.store'), [
            'trim_id' => $trim->id,
            'condition' => 'new',
            'stock_code' => 'MZD-001',
            'vin' => 'VIN0011223344',
            'year' => 2025,
            'mileage' => null,
            'body_type_id' => $bodyType->id,
            'price' => 850000000,
            'currency' => 'VND',
            'status' => 'available',
            'notes_internal' => 'Xe demo showroom',
            'media' => [
                [
                    'type' => 'image',
                    'path_or_url' => 'boxcar/images/resource/shop3-1.jpg',
                    'caption' => 'Front view',
                    'sort_order' => 0,
                    'is_cover' => true,
                ],
            ],
        ]);

        $carUnit = CarUnit::query()->where('stock_code', 'MZD-001')->firstOrFail();

        $createResponse->assertRedirect(route('admin.inventory.edit', $carUnit));
        $this->assertDatabaseHas('car_units', [
            'id' => $carUnit->id,
            'status' => 'available',
            'price' => 850000000,
        ]);
        $this->assertDatabaseHas('car_unit_media', [
            'car_unit_id' => $carUnit->id,
            'path_or_url' => 'boxcar/images/resource/shop3-1.jpg',
            'is_cover' => 1,
        ]);

        $this->actingAs($admin)->post(route('admin.inventory.hold', $carUnit), [
            'hold_until' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'reason' => 'Khach da coc',
        ])->assertRedirect();

        $this->assertDatabaseHas('car_units', [
            'id' => $carUnit->id,
            'status' => 'on_hold',
        ]);
        $this->assertDatabaseHas('car_unit_holds', [
            'car_unit_id' => $carUnit->id,
            'created_by' => $admin->id,
            'reason' => 'Khach da coc',
        ]);

        $this->actingAs($admin)->post(route('admin.inventory.price.update', $carUnit), [
            'price' => 820000000,
        ])->assertRedirect();

        $this->assertDatabaseHas('car_unit_price_histories', [
            'car_unit_id' => $carUnit->id,
            'changed_by' => $admin->id,
            'old_price' => 850000000,
            'new_price' => 820000000,
        ]);

        $lead = Lead::query()->create([
            'car_unit_id' => $carUnit->id,
            'trim_id' => $trim->id,
            'assigned_to' => $admin->id,
            'source' => 'unit_detail',
            'name' => 'Prospect Buyer',
            'phone' => '0909998888',
            'status' => 'booked',
        ]);

        $saleResponse = $this->actingAs($admin)->post(route('admin.sales.store'), [
            'car_unit_id' => $carUnit->id,
            'buyer_name' => 'Prospect Buyer',
            'buyer_phone' => '0909998888',
            'lead_id' => $lead->id,
            'sold_price' => 815000000,
            'sold_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $saleResponse->assertRedirect(route('admin.sales.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Prospect Buyer',
            'phone' => '0909998888',
        ]);
        $this->assertDatabaseHas('sales', [
            'car_unit_id' => $carUnit->id,
            'created_by' => $admin->id,
            'sold_price' => 815000000,
        ]);
        $this->assertDatabaseHas('car_units', [
            'id' => $carUnit->id,
            'status' => 'sold',
        ]);
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => 'closed',
        ]);
    }
}
