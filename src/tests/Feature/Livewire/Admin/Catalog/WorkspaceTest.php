<?php

namespace Tests\Feature\Livewire\Admin\Catalog;

use App\Livewire\Admin\Catalog\Makes\Manager as MakesManager;
use App\Models\Make;
use App\Models\User;
use Database\Seeders\UsersAndRbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_unified_catalog_workspace(): void
    {
        $this->seed(UsersAndRbacSeeder::class);

        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.catalog.index'));

        $response->assertOk();
        $response->assertSeeLivewire('admin.catalog.page');
        $response->assertSeeLivewire('admin.catalog.makes.manager');
        $response->assertSeeText('Architecture');
        $response->assertSeeText('Makes');
        $response->assertSeeText('Models');
        $response->assertSeeText('Trims');
    }

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

    public function test_admin_can_create_make_with_uploaded_logo_from_livewire_workspace(): void
    {
        Storage::fake('public');
        $this->seed(UsersAndRbacSeeder::class);

        $admin = User::query()->where('email', 'admin@showroom.test')->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(MakesManager::class)
            ->set('createForm.name', 'Toyota')
            ->set('createForm.slug', 'toyota')
            ->set('logoUpload', UploadedFile::fake()->image('toyota.png', 240, 240))
            ->call('create')
            ->assertHasNoErrors();

        $make = Make::query()->where('slug', 'toyota')->firstOrFail();

        $this->assertNotNull($make->logo_path);
        Storage::disk('public')->assertExists($make->logo_path);
    }
}
