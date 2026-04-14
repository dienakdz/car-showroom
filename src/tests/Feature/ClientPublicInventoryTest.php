<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClientPublicInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_inventory_lists_only_available_and_published_units(): void
    {
        $catalog = $this->createCatalogFixture();

        $visibleStock = $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'VISIBLE-NEW-001',
            'condition' => 'new',
            'status' => 'available',
            'published_at' => now()->subDay(),
        ]);

        $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'HIDDEN-DRAFT-001',
            'condition' => 'new',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'HIDDEN-SOLD-001',
            'condition' => 'new',
            'status' => 'sold',
            'published_at' => now()->subDay(),
            'sold_at' => now(),
        ]);

        $response = $this->get(route('inventory.new'));

        $response->assertOk();
        $response->assertSee(route('car.show', ['stockCode' => $visibleStock]));
        $response->assertDontSee(route('car.show', ['stockCode' => 'HIDDEN-DRAFT-001']));
        $response->assertDontSee(route('car.show', ['stockCode' => 'HIDDEN-SOLD-001']));
    }

    public function test_public_car_detail_returns_404_for_unpublished_or_unavailable_units(): void
    {
        $catalog = $this->createCatalogFixture();

        $unpublishedStock = $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'UNPUBLISHED-001',
            'status' => 'available',
            'published_at' => null,
        ]);

        $soldStock = $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'SOLD-001',
            'status' => 'sold',
            'published_at' => now()->subDay(),
            'sold_at' => now(),
        ]);

        $this->get(route('car.show', ['stockCode' => $unpublishedStock]))->assertNotFound();
        $this->get(route('car.show', ['stockCode' => $soldStock]))->assertNotFound();
    }

    public function test_public_inventory_supports_trim_and_mileage_filters(): void
    {
        $catalog = $this->createCatalogFixture();

        $matchingStock = $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'MATCH-USED-001',
            'condition' => 'used',
            'mileage' => 15000,
            'status' => 'available',
            'published_at' => now()->subDays(2),
        ]);

        $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'LOW-MILEAGE-001',
            'condition' => 'used',
            'mileage' => 5000,
            'status' => 'available',
            'published_at' => now()->subDays(3),
        ]);

        $this->createCarUnit($catalog['trim_secondary_id'], [
            'stock_code' => 'OTHER-TRIM-001',
            'condition' => 'used',
            'mileage' => 16000,
            'status' => 'available',
            'published_at' => now()->subDays(4),
        ]);

        $response = $this->get(route('inventory.index', [
            'condition' => 'used',
            'trim' => $catalog['trim_primary_slug'],
            'min_mileage' => 10000,
            'max_mileage' => 20000,
            'sort' => 'mileage_asc',
        ]));

        $response->assertOk();
        $response->assertSee(route('car.show', ['stockCode' => $matchingStock]));
        $response->assertDontSee(route('car.show', ['stockCode' => 'LOW-MILEAGE-001']));
        $response->assertDontSee(route('car.show', ['stockCode' => 'OTHER-TRIM-001']));
    }

    public function test_public_inventory_supports_year_range_filters(): void
    {
        $catalog = $this->createCatalogFixture();

        $matchingStock = $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'YEAR-RANGE-2024',
            'condition' => 'used',
            'year' => 2024,
            'status' => 'available',
            'published_at' => now()->subDay(),
        ]);

        $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'YEAR-RANGE-2022',
            'condition' => 'used',
            'year' => 2022,
            'status' => 'available',
            'published_at' => now()->subDays(2),
        ]);

        $this->createCarUnit($catalog['trim_primary_id'], [
            'stock_code' => 'YEAR-RANGE-2025',
            'condition' => 'used',
            'year' => 2025,
            'status' => 'available',
            'published_at' => now()->subDays(3),
        ]);

        $response = $this->get(route('inventory.index', [
            'condition' => 'used',
            'min_year' => 2023,
            'max_year' => 2024,
        ]));

        $response->assertOk();
        $response->assertSee(route('car.show', ['stockCode' => $matchingStock]));
        $response->assertDontSee(route('car.show', ['stockCode' => 'YEAR-RANGE-2022']));
        $response->assertDontSee(route('car.show', ['stockCode' => 'YEAR-RANGE-2025']));
    }

    private function createCatalogFixture(): array
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

        $trimPrimaryId = DB::table('trims')->insertGetId([
            'model_id' => $modelId,
            'name' => 'RS',
            'slug' => 'civic-rs',
            'year_from' => 2024,
            'year_to' => null,
            'msrp' => 780000000,
            'description' => 'Trim chinh cho public inventory.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $trimSecondaryId = DB::table('trims')->insertGetId([
            'model_id' => $modelId,
            'name' => 'G',
            'slug' => 'civic-g',
            'year_from' => 2024,
            'year_to' => null,
            'msrp' => 720000000,
            'description' => 'Trim phu de test bo loc.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'trim_primary_id' => $trimPrimaryId,
            'trim_primary_slug' => 'civic-rs',
            'trim_secondary_id' => $trimSecondaryId,
        ];
    }

    private function createCarUnit(int $trimId, array $overrides = []): string
    {
        $stockCode = $overrides['stock_code'] ?? ('STOCK-' . uniqid());

        DB::table('car_units')->insert([
            'trim_id' => $trimId,
            'condition' => $overrides['condition'] ?? 'used',
            'vin' => $overrides['vin'] ?? ('VIN-' . $stockCode),
            'stock_code' => $stockCode,
            'year' => $overrides['year'] ?? 2024,
            'mileage' => $overrides['mileage'] ?? 12000,
            'body_type_id' => $overrides['body_type_id'] ?? null,
            'fuel_type_id' => $overrides['fuel_type_id'] ?? null,
            'transmission_id' => $overrides['transmission_id'] ?? null,
            'drivetrain_id' => $overrides['drivetrain_id'] ?? null,
            'exterior_color_id' => $overrides['exterior_color_id'] ?? null,
            'interior_color_id' => $overrides['interior_color_id'] ?? null,
            'price' => $overrides['price'] ?? 780000000,
            'currency' => $overrides['currency'] ?? 'VND',
            'status' => $overrides['status'] ?? 'available',
            'hold_until' => $overrides['hold_until'] ?? null,
            'published_at' => array_key_exists('published_at', $overrides) ? $overrides['published_at'] : now()->subDay(),
            'sold_at' => array_key_exists('sold_at', $overrides) ? $overrides['sold_at'] : null,
            'notes_internal' => $overrides['notes_internal'] ?? null,
            'deleted_at' => $overrides['deleted_at'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $stockCode;
    }
}
