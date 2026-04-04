<?php

namespace Tests\Feature\Admin;

use App\Models\CarAttribute;
use App\Models\CarModel;
use App\Models\Feature;
use App\Models\FeatureGroup;
use App\Models\Make;
use App\Models\Trim;
use App\Models\User;
use Database\Seeders\UsersAndRbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_make_model_and_trim_with_metadata(): void
    {
        $this->seed(UsersAndRbacSeeder::class);

        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();
        $featureGroup = FeatureGroup::query()->create([
            'name' => 'Comfort',
            'sort_order' => 1,
        ]);
        $feature = Feature::query()->create([
            'feature_group_id' => $featureGroup->id,
            'name' => 'Adaptive Cruise Control',
            'slug' => 'adaptive-cruise-control',
        ]);
        $attribute = CarAttribute::query()->create([
            'code' => 'sunroof',
            'label' => 'Sunroof',
            'type' => 'boolean',
            'unit' => null,
            'is_filterable' => false,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)->post(route('admin.catalog.makes.store'), [
            'name' => 'Honda',
            'slug' => 'honda',
        ])->assertRedirect(route('admin.catalog.makes.index'));

        $make = Make::query()->where('slug', 'honda')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.catalog.models.store'), [
            'make_id' => $make->id,
            'name' => 'Civic',
            'slug' => 'civic',
        ])->assertRedirect(route('admin.catalog.models.index'));

        $model = CarModel::query()->where('slug', 'civic')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.catalog.trims.store'), [
            'model_id' => $model->id,
            'name' => 'RS',
            'slug' => 'rs',
            'year_from' => 2024,
            'year_to' => 2026,
            'msrp' => 890000000,
            'description' => 'Ban RS danh cho nhu cau premium.',
            'feature_ids' => [$feature->id],
            'attributes' => [
                $attribute->id => [
                    'value_boolean' => '1',
                ],
            ],
        ]);

        $trim = Trim::query()->where('slug', 'rs')->firstOrFail();

        $response->assertRedirect(route('admin.catalog.trims.edit', $trim));
        $this->assertDatabaseHas('trims', [
            'id' => $trim->id,
            'model_id' => $model->id,
            'name' => 'RS',
        ]);
        $this->assertDatabaseHas('trim_feature', [
            'trim_id' => $trim->id,
            'feature_id' => $feature->id,
        ]);
        $this->assertDatabaseHas('trim_attribute_values', [
            'trim_id' => $trim->id,
            'attribute_id' => $attribute->id,
            'value_boolean' => 1,
        ]);
    }
}
