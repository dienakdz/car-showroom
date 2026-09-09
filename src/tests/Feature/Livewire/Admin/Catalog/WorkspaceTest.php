<?php

namespace Tests\Feature\Livewire\Admin\Catalog;

use App\Livewire\Admin\Catalog\Models\Manager as ModelsManager;
use App\Livewire\Admin\Catalog\Trims\Manager as TrimsManager;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Trim;
use App\Models\User;
use Database\Seeders\UsersAndRbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_catalog_routes_redirect_to_the_unified_workspace(): void
    {
        $this->seed(UsersAndRbacSeeder::class);

        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.catalog.makes.index'))
            ->assertRedirect(route('admin.catalog.index', ['tab' => 'makes']));

        $this->actingAs($admin)
            ->get(route('admin.catalog.models.index'))
            ->assertRedirect(route('admin.catalog.index', ['tab' => 'models']));

        $this->actingAs($admin)
            ->get(route('admin.catalog.trims.index'))
            ->assertRedirect(route('admin.catalog.index', ['tab' => 'trims']));
    }

    public function test_models_manager_renders_with_template_table_layout(): void
    {
        $make = Make::query()->create([
            'name' => 'Toyota',
            'slug' => 'toyota',
        ]);

        CarModel::query()->create([
            'make_id' => $make->id,
            'name' => 'Corolla Cross',
            'slug' => 'corolla-cross',
        ]);

        Livewire::test(ModelsManager::class)
            ->assertSee('Tạo dòng xe mới')
            ->assertSee('Corolla Cross');
    }

    public function test_trims_manager_renders_with_template_table_layout(): void
    {
        $make = Make::query()->create([
            'name' => 'Honda',
            'slug' => 'honda',
        ]);

        $model = CarModel::query()->create([
            'make_id' => $make->id,
            'name' => 'Civic',
            'slug' => 'civic',
        ]);

        Trim::query()->create([
            'model_id' => $model->id,
            'name' => 'RS',
            'slug' => 'rs',
            'description' => 'Ban RS',
        ]);

        Livewire::test(TrimsManager::class)
            ->assertSee('Tạo phiên bản xe')
            ->assertSee('RS');
    }
}
