<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupSeeder extends Seeder
{
    /**
     * Seed lookup tables used by inventory.
     */
    public function run(): void
    {
        $lookupTables = [
            'body_types' => [
                ['name' => 'Sedan', 'slug' => 'sedan'],
                ['name' => 'SUV', 'slug' => 'suv'],
                ['name' => 'Hatchback', 'slug' => 'hatchback'],
                ['name' => 'Pickup', 'slug' => 'pickup'],
            ],
            'fuel_types' => [
                ['name' => 'Gasoline', 'slug' => 'gasoline'],
                ['name' => 'Diesel', 'slug' => 'diesel'],
                ['name' => 'Hybrid', 'slug' => 'hybrid'],
                ['name' => 'Electric', 'slug' => 'electric'],
            ],
            'transmissions' => [
                ['name' => 'Manual', 'slug' => 'manual'],
                ['name' => 'Automatic', 'slug' => 'automatic'],
                ['name' => 'CVT', 'slug' => 'cvt'],
            ],
            'drivetrains' => [
                ['name' => 'FWD', 'slug' => 'fwd'],
                ['name' => 'RWD', 'slug' => 'rwd'],
                ['name' => 'AWD', 'slug' => 'awd'],
                ['name' => '4WD', 'slug' => '4wd'],
            ],
        ];

        foreach ($lookupTables as $table => $rows) {
            foreach ($rows as $row) {
                DB::table($table)->updateOrInsert(['slug' => $row['slug']], $row);
            }
        }

        $colors = [
            ['name' => 'Pearl White', 'slug' => 'pearl-white', 'type' => 'exterior', 'hex' => '#F2F2F2'],
            ['name' => 'Obsidian Black', 'slug' => 'obsidian-black', 'type' => 'exterior', 'hex' => '#111111'],
            ['name' => 'Candy Red', 'slug' => 'candy-red', 'type' => 'exterior', 'hex' => '#C21807'],
            ['name' => 'Ocean Blue', 'slug' => 'ocean-blue', 'type' => 'exterior', 'hex' => '#1E4F8A'],
            ['name' => 'Cabin Black', 'slug' => 'cabin-black', 'type' => 'interior', 'hex' => '#1A1A1A'],
            ['name' => 'Cabin Brown', 'slug' => 'cabin-brown', 'type' => 'interior', 'hex' => '#5B3A29'],
            ['name' => 'Cabin Beige', 'slug' => 'cabin-beige', 'type' => 'interior', 'hex' => '#CBB79A'],
        ];

        foreach ($colors as $color) {
            DB::table('colors')->updateOrInsert(['slug' => $color['slug']], $color);
        }
    }
}
