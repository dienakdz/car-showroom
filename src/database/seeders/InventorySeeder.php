<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Seed car units and inventory-related logs.
     */
    public function run(): void
    {
        $now = now();

        $trimIds = DB::table('trims')
            ->whereIn('slug', ['vios-g', 'corolla-cross-hybrid', 'civic-rs', 'city-rs', 'ranger-wildtrak'])
            ->pluck('id', 'slug');

        $bodyTypeIds = DB::table('body_types')
            ->whereIn('slug', ['sedan', 'suv', 'pickup'])
            ->pluck('id', 'slug');

        $fuelTypeIds = DB::table('fuel_types')
            ->whereIn('slug', ['gasoline', 'diesel', 'hybrid'])
            ->pluck('id', 'slug');

        $transmissionIds = DB::table('transmissions')
            ->whereIn('slug', ['automatic', 'cvt'])
            ->pluck('id', 'slug');

        $drivetrainIds = DB::table('drivetrains')
            ->whereIn('slug', ['fwd', '4wd'])
            ->pluck('id', 'slug');

        $colorIds = DB::table('colors')
            ->whereIn('slug', ['pearl-white', 'obsidian-black', 'candy-red', 'ocean-blue', 'cabin-black', 'cabin-brown', 'cabin-beige'])
            ->pluck('id', 'slug');

        $units = [
            [
                'trim_slug' => 'civic-rs',
                'condition' => 'new',
                'vin' => 'JHMFC1001SL000001',
                'stock_code' => 'NEW-CIVIC-001',
                'year' => 2025,
                'mileage' => null,
                'body_type_slug' => 'sedan',
                'fuel_type_slug' => 'gasoline',
                'transmission_slug' => 'cvt',
                'drivetrain_slug' => 'fwd',
                'exterior_color_slug' => 'candy-red',
                'interior_color_slug' => 'cabin-black',
                'price' => 865000000,
                'currency' => 'VND',
                'status' => 'available',
                'hold_until' => null,
                'published_at' => $now->copy()->subDays(3),
                'sold_at' => null,
                'notes_internal' => 'Xe demo ưu tiên cho lượng khách cuối tuần.',
            ],
            [
                'trim_slug' => 'corolla-cross-hybrid',
                'condition' => 'used',
                'vin' => 'JTNGB1002SL000002',
                'stock_code' => 'USED-CROSS-001',
                'year' => 2023,
                'mileage' => 23500,
                'body_type_slug' => 'suv',
                'fuel_type_slug' => 'hybrid',
                'transmission_slug' => 'automatic',
                'drivetrain_slug' => 'fwd',
                'exterior_color_slug' => 'pearl-white',
                'interior_color_slug' => 'cabin-beige',
                'price' => 825000000,
                'currency' => 'VND',
                'status' => 'on_hold',
                'hold_until' => $now->copy()->addDays(2),
                'published_at' => $now->copy()->subDays(20),
                'sold_at' => null,
                'notes_internal' => 'Khách yêu cầu giữ xe trong lúc chờ ngân hàng phê duyệt.',
            ],
            [
                'trim_slug' => 'ranger-wildtrak',
                'condition' => 'used',
                'vin' => 'MNBAB3003SL000003',
                'stock_code' => 'USED-RANGER-001',
                'year' => 2022,
                'mileage' => 41000,
                'body_type_slug' => 'pickup',
                'fuel_type_slug' => 'diesel',
                'transmission_slug' => 'automatic',
                'drivetrain_slug' => '4wd',
                'exterior_color_slug' => 'obsidian-black',
                'interior_color_slug' => 'cabin-brown',
                'price' => 920000000,
                'currency' => 'VND',
                'status' => 'sold',
                'hold_until' => null,
                'published_at' => $now->copy()->subDays(60),
                'sold_at' => $now->copy()->subDays(5),
                'notes_internal' => 'Xe trade-in có lịch sử bảo dưỡng đầy đủ.',
            ],
            [
                'trim_slug' => 'vios-g',
                'condition' => 'new',
                'vin' => 'RLVAB4004SL000004',
                'stock_code' => 'NEW-VIOS-001',
                'year' => 2025,
                'mileage' => 50,
                'body_type_slug' => 'sedan',
                'fuel_type_slug' => 'gasoline',
                'transmission_slug' => 'cvt',
                'drivetrain_slug' => 'fwd',
                'exterior_color_slug' => 'ocean-blue',
                'interior_color_slug' => 'cabin-beige',
                'price' => 515000000,
                'currency' => 'VND',
                'status' => 'draft',
                'hold_until' => null,
                'published_at' => null,
                'sold_at' => null,
                'notes_internal' => 'Tin đăng nháp, đang chờ bổ sung bộ ảnh đầy đủ.',
            ],
            [
                'trim_slug' => 'city-rs',
                'condition' => 'cpo',
                'vin' => 'RLHAB5005SL000005',
                'stock_code' => 'CPO-CITY-001',
                'year' => 2024,
                'mileage' => 9000,
                'body_type_slug' => 'sedan',
                'fuel_type_slug' => 'gasoline',
                'transmission_slug' => 'cvt',
                'drivetrain_slug' => 'fwd',
                'exterior_color_slug' => 'pearl-white',
                'interior_color_slug' => 'cabin-black',
                'price' => 585000000,
                'currency' => 'VND',
                'status' => 'available',
                'hold_until' => null,
                'published_at' => $now->copy()->subDays(7),
                'sold_at' => null,
                'notes_internal' => 'Xe CPO kèm gia hạn bảo hành 1 năm.',
            ],
        ];

        foreach ($units as $unit) {
            DB::table('car_units')->updateOrInsert(
                ['stock_code' => $unit['stock_code']],
                [
                    'trim_id' => $trimIds[$unit['trim_slug']],
                    'condition' => $unit['condition'],
                    'vin' => $unit['vin'],
                    'year' => $unit['year'],
                    'mileage' => $unit['mileage'],
                    'body_type_id' => $bodyTypeIds[$unit['body_type_slug']],
                    'fuel_type_id' => $fuelTypeIds[$unit['fuel_type_slug']],
                    'transmission_id' => $transmissionIds[$unit['transmission_slug']],
                    'drivetrain_id' => $drivetrainIds[$unit['drivetrain_slug']],
                    'exterior_color_id' => $colorIds[$unit['exterior_color_slug']],
                    'interior_color_id' => $colorIds[$unit['interior_color_slug']],
                    'price' => $unit['price'],
                    'currency' => $unit['currency'],
                    'status' => $unit['status'],
                    'hold_until' => $unit['hold_until'],
                    'published_at' => $unit['published_at'],
                    'sold_at' => $unit['sold_at'],
                    'notes_internal' => $unit['notes_internal'],
                    'deleted_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $unitIds = DB::table('car_units')
            ->whereIn('stock_code', array_column($units, 'stock_code'))
            ->pluck('id', 'stock_code');

        DB::table('car_unit_media')->delete();
        $mediaRows = [
            ['stock_code' => 'NEW-CIVIC-001', 'type' => 'image', 'path_or_url' => '/seed-media/civic-rs-1.jpg', 'caption' => 'Góc chụp đầu xe', 'sort_order' => 1, 'is_cover' => true],
            ['stock_code' => 'NEW-CIVIC-001', 'type' => 'image', 'path_or_url' => '/seed-media/civic-rs-2.jpg', 'caption' => 'Khoang táp-lô', 'sort_order' => 2, 'is_cover' => false],
            ['stock_code' => 'USED-CROSS-001', 'type' => 'image', 'path_or_url' => '/seed-media/cross-hybrid-1.jpg', 'caption' => 'Ngoại thất thân xe', 'sort_order' => 1, 'is_cover' => true],
            ['stock_code' => 'USED-CROSS-001', 'type' => 'video', 'path_or_url' => 'https://example.com/videos/cross-walkaround.mp4', 'caption' => 'Video walkaround', 'sort_order' => 2, 'is_cover' => false],
            ['stock_code' => 'USED-RANGER-001', 'type' => 'image', 'path_or_url' => '/seed-media/ranger-1.jpg', 'caption' => 'Thiết lập off-road', 'sort_order' => 1, 'is_cover' => true],
            ['stock_code' => 'CPO-CITY-001', 'type' => 'image', 'path_or_url' => '/seed-media/city-rs-1.jpg', 'caption' => 'Đầu xe City RS', 'sort_order' => 1, 'is_cover' => true],
        ];

        foreach ($mediaRows as $media) {
            DB::table('car_unit_media')->insert([
                'car_unit_id' => $unitIds[$media['stock_code']],
                'type' => $media['type'],
                'path_or_url' => $media['path_or_url'],
                'caption' => $media['caption'],
                'sort_order' => $media['sort_order'],
                'is_cover' => $media['is_cover'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $staffId = DB::table('users')->where('email', 'staff@showroom.test')->value('id');

        DB::table('car_unit_holds')->delete();
        DB::table('car_unit_holds')->insert([
            'car_unit_id' => $unitIds['USED-CROSS-001'],
            'created_by' => $staffId,
            'hold_until' => $now->copy()->addDays(2),
            'reason' => 'Đang chờ hồ sơ vay mua xe từ khách hàng.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('car_unit_price_histories')->delete();
        DB::table('car_unit_price_histories')->insert([
            [
                'car_unit_id' => $unitIds['NEW-CIVIC-001'],
                'changed_by' => $staffId,
                'old_price' => 879000000,
                'new_price' => 865000000,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'car_unit_id' => $unitIds['USED-CROSS-001'],
                'changed_by' => $staffId,
                'old_price' => 845000000,
                'new_price' => 825000000,
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'car_unit_id' => $unitIds['USED-RANGER-001'],
                'changed_by' => $staffId,
                'old_price' => 945000000,
                'new_price' => 920000000,
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ],
        ]);
    }
}
