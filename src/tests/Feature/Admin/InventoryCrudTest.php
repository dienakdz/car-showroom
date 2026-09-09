<?php

namespace Tests\Feature\Admin;

use App\Models\CarUnit;
use App\Models\Trim;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\FeaturesAndAttributesSeeder;
use Database\Seeders\InventorySeeder;
use Database\Seeders\LookupSeeder;
use Database\Seeders\UsersAndRbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UsersAndRbacSeeder::class,
            CatalogSeeder::class,
            LookupSeeder::class,
            FeaturesAndAttributesSeeder::class,
            InventorySeeder::class,
        ]);
    }

    public function test_admin_can_view_inventory_index(): void
    {
        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.inventory.index'));

        $response->assertOk();
        $response->assertSeeText('Quản lý kho xe');
        $response->assertSeeText('Sẵn sàng bán');
    }

    public function test_admin_can_view_inventory_create_form(): void
    {
        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.inventory.create'));

        $response->assertOk();
        $response->assertSeeText('Thêm xe mới vào kho');
        $response->assertSee('1. Thông tin định danh');
        $response->assertSee('2. Thông số kỹ thuật');
        $response->assertSee('3. Định giá');
        $response->assertSee('4. Hình ảnh xe');
        $response->assertSee('Xem trước hiển thị (Live Preview)');
    }

    public function test_admin_can_create_new_car_unit(): void
    {
        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();
        $trim = Trim::query()->firstOrFail();

        $payload = [
            'trim_id' => $trim->id,
            'condition' => 'new',
            'stock_code' => 'STK-TEST-NEW-01',
            'vin' => 'VIN-AUTO-TEST-0001',
            'year' => 2025,
            'mileage' => 0,
            'price' => 950000000,
            'currency' => 'VND',
            'status' => 'available',
            'notes_internal' => 'Ghi chú kiểm tra hệ thống',
            'media' => [
                [
                    'type' => 'image',
                    'path_or_url' => '/storage/inventory-media/test-car.jpg',
                    'caption' => 'Ảnh đầu xe',
                    'sort_order' => 0,
                    'is_cover' => '1',
                ],
            ],
        ];

        $response = $this->actingAs($admin)->post(route('admin.inventory.store'), $payload);

        $created = CarUnit::query()->where('stock_code', 'STK-TEST-NEW-01')->first();
        $this->assertNotNull($created);
        $this->assertSame('available', $created->status);
        $this->assertSame(950000000, $created->price);
        $this->assertCount(1, $created->media);
        /** @var \App\Models\CarUnitMedia $firstMedia */
        $firstMedia = $created->media->first();
        $this->assertTrue((bool) $firstMedia->is_cover);

        $response->assertRedirect(route('admin.inventory.edit', $created));
    }

    public function test_admin_can_update_car_unit_and_price_history(): void
    {
        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();
        $carUnit = CarUnit::query()->firstOrFail();
        $oldPrice = $carUnit->price;
        $newPrice = $oldPrice ? $oldPrice + 20000000 : 900000000;

        $payload = [
            'trim_id' => $carUnit->trim_id,
            'condition' => $carUnit->condition,
            'stock_code' => $carUnit->stock_code,
            'vin' => $carUnit->vin,
            'year' => $carUnit->year,
            'mileage' => $carUnit->mileage ?: 100,
            'price' => $newPrice,
            'currency' => 'VND',
            'status' => $carUnit->status === 'sold' ? 'available' : $carUnit->status,
            'notes_internal' => 'Đã cập nhật giá mới',
            'media' => [
                [
                    'type' => 'image',
                    'path_or_url' => '/seed-media/civic-rs-1.jpg',
                    'caption' => 'Ảnh cập nhật',
                    'sort_order' => 0,
                    'is_cover' => '1',
                ],
            ],
        ];

        $response = $this->actingAs($admin)->patch(route('admin.inventory.update', $carUnit), $payload);

        $response->assertRedirect(route('admin.inventory.edit', $carUnit));

        $carUnit->refresh();
        $this->assertSame($newPrice, $carUnit->price);
        $this->assertSame('Đã cập nhật giá mới', $carUnit->notes_internal);
        $this->assertDatabaseHas('car_unit_price_histories', [
            'car_unit_id' => $carUnit->id,
            'new_price' => $newPrice,
        ]);
    }

    public function test_admin_can_upload_media_via_ajax(): void
    {
        Storage::fake('public');
        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();

        $file = UploadedFile::fake()->create('test_car_upload.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($admin)->postJson(route('admin.inventory.media.upload'), [
            'file' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'path_or_url',
            'filename',
        ]);
        $this->assertTrue($response->json('success'));

        $path = str_replace('/storage/', '', $response->json('path_or_url'));
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_publish_and_archive_car_unit(): void
    {
        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();
        $carUnit = CarUnit::query()->where('status', '!=', 'sold')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.inventory.archive', $carUnit));
        $carUnit->refresh();
        $this->assertSame('archived', $carUnit->status);

        $this->actingAs($admin)->post(route('admin.inventory.publish', $carUnit));
        $carUnit->refresh();
        $this->assertSame('available', $carUnit->status);
    }
}
