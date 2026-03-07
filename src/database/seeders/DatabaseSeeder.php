<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersAndRbacSeeder::class,
            CatalogSeeder::class,
            LookupSeeder::class,
            FeaturesAndAttributesSeeder::class,
            InventorySeeder::class,
            CrmSalesSeeder::class,
            ShowroomSettingsSeeder::class,
        ]);
    }
}
