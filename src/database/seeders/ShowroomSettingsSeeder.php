<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShowroomSettingsSeeder extends Seeder
{
    /**
     * Seed showroom profile and key/value settings.
     */
    public function run(): void
    {
        $now = now();

        DB::table('showrooms')->updateOrInsert(
            ['name' => 'MD-CARS Showroom'],
            [
                'phone' => '0900000002',
                'email' => 'contact@md-cars.vn',
                'address' => '123 Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh',
                'description' => 'Showroom ô tô cao cấp MD-CARS, chuyên các dòng xe mới chính hãng và xe lướt chọn lọc kiểm định 160 bước.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $settings = [
            'site.brand_name' => ['value' => 'MD-CARS Showroom'],
            'site.default_currency' => ['value' => 'VND'],
            'inventory.filters' => ['conditions' => ['new', 'used', 'cpo'], 'default_status' => 'available'],
            'contact.sales_hotline' => ['value' => '0900000002'],
            'homepage.hero' => [
                'title' => 'Tìm chiếc xe tiếp theo của bạn',
                'subtitle' => 'Khám phá kho xe mới, xe đã qua sử dụng và xe CPO.',
            ],
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value_json' => json_encode($value, JSON_UNESCAPED_UNICODE),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
