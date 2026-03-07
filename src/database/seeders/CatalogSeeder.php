<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    /**
     * Seed makes, models, and trims.
     */
    public function run(): void
    {
        $now = now();

        $makes = [
            ['name' => 'Toyota', 'slug' => 'toyota', 'logo_path' => null],
            ['name' => 'Honda', 'slug' => 'honda', 'logo_path' => null],
            ['name' => 'Ford', 'slug' => 'ford', 'logo_path' => null],
        ];

        foreach ($makes as $make) {
            DB::table('makes')->updateOrInsert(
                ['slug' => $make['slug']],
                array_merge($make, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        $makeIds = DB::table('makes')
            ->whereIn('slug', ['toyota', 'honda', 'ford'])
            ->pluck('id', 'slug');

        $models = [
            ['make_slug' => 'toyota', 'name' => 'Vios', 'slug' => 'vios'],
            ['make_slug' => 'toyota', 'name' => 'Corolla Cross', 'slug' => 'corolla-cross'],
            ['make_slug' => 'honda', 'name' => 'Civic', 'slug' => 'civic'],
            ['make_slug' => 'honda', 'name' => 'City', 'slug' => 'city'],
            ['make_slug' => 'ford', 'name' => 'Ranger', 'slug' => 'ranger'],
        ];

        foreach ($models as $model) {
            $makeId = $makeIds[$model['make_slug']];
            DB::table('models')->updateOrInsert(
                ['make_id' => $makeId, 'slug' => $model['slug']],
                [
                    'name' => $model['name'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $modelIds = DB::table('models')
            ->whereIn('slug', ['vios', 'corolla-cross', 'civic', 'city', 'ranger'])
            ->pluck('id', 'slug');

        $trims = [
            [
                'model_slug' => 'vios',
                'name' => 'Vios E',
                'slug' => 'vios-e',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 458000000,
                'description' => 'Bản sedan tiêu chuẩn, phù hợp di chuyển trong đô thị.',
            ],
            [
                'model_slug' => 'vios',
                'name' => 'Vios G',
                'slug' => 'vios-g',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 520000000,
                'description' => 'Phiên bản cao hơn với nhiều trang bị tiện nghi hơn.',
            ],
            [
                'model_slug' => 'corolla-cross',
                'name' => 'Corolla Cross Hybrid',
                'slug' => 'corolla-cross-hybrid',
                'year_from' => 2025,
                'year_to' => null,
                'msrp' => 905000000,
                'description' => 'Bản SUV Hybrid tiết kiệm nhiên liệu tốt.',
            ],
            [
                'model_slug' => 'civic',
                'name' => 'Civic RS',
                'slug' => 'civic-rs',
                'year_from' => 2025,
                'year_to' => null,
                'msrp' => 870000000,
                'description' => 'Bản sedan Turbo thiên hướng thể thao.',
            ],
            [
                'model_slug' => 'city',
                'name' => 'City RS',
                'slug' => 'city-rs',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 599000000,
                'description' => 'Bản sedan cỡ nhỏ với phong cách RS.',
            ],
            [
                'model_slug' => 'ranger',
                'name' => 'Ranger Wildtrak',
                'slug' => 'ranger-wildtrak',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 965000000,
                'description' => 'Bản pickup trang bị tốt, phù hợp nhiều nhu cầu sử dụng.',
            ],
        ];

        foreach ($trims as $trim) {
            $modelId = $modelIds[$trim['model_slug']];
            DB::table('trims')->updateOrInsert(
                ['model_id' => $modelId, 'slug' => $trim['slug']],
                [
                    'name' => $trim['name'],
                    'year_from' => $trim['year_from'],
                    'year_to' => $trim['year_to'],
                    'msrp' => $trim['msrp'],
                    'description' => $trim['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
