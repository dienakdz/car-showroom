<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesAndAttributesSeeder extends Seeder
{
    /**
     * Seed feature catalogs, specifications, and trim mappings.
     */
    public function run(): void
    {
        // 1. Feature Groups
        $featureGroups = [
            ['name' => 'An toàn', 'sort_order' => 10],
            ['name' => 'Tiện nghi & Nội thất', 'sort_order' => 20],
            ['name' => 'Công nghệ & Giải trí', 'sort_order' => 30],
            ['name' => 'Vận hành & Ngoại thất', 'sort_order' => 40],
        ];

        foreach ($featureGroups as $group) {
            DB::table('feature_groups')->updateOrInsert(['name' => $group['name']], $group);
        }

        $featureGroupIds = DB::table('feature_groups')->pluck('id', 'name');

        // 2. Features
        $features = [
            // An toàn
            ['feature_group' => 'An toàn', 'name' => 'Hệ thống chống bó cứng phanh ABS & EBD', 'slug' => 'abs'],
            ['feature_group' => 'An toàn', 'name' => 'Hệ thống 6 - 7 túi khí an toàn', 'slug' => 'six-airbags'],
            ['feature_group' => 'An toàn', 'name' => 'Cảnh báo điểm mù (BSM) & Cắt ngang (RCTA)', 'slug' => 'blind-spot-monitoring'],
            ['feature_group' => 'An toàn', 'name' => 'Gói an toàn chủ động ADAS (TSS / Sensing / Co-Pilot360 / SmartSense)', 'slug' => 'adas'],
            ['feature_group' => 'An toàn', 'name' => 'Camera toàn cảnh 360 độ siêu nét', 'slug' => 'camera-360'],
            ['feature_group' => 'An toàn', 'name' => 'Cảm biến va chạm trước & sau', 'slug' => 'parking-sensors'],

            // Tiện nghi & Nội thất
            ['feature_group' => 'Tiện nghi & Nội thất', 'name' => 'Ghế bọc da cao cấp chỉnh điện nhớ vị trí', 'slug' => 'leather-seats'],
            ['feature_group' => 'Tiện nghi & Nội thất', 'name' => 'Cửa sổ trời toàn cảnh Panorama', 'slug' => 'panoramic-sunroof'],
            ['feature_group' => 'Tiện nghi & Nội thất', 'name' => 'Hệ thống điều hòa tự động đa vùng độc lập', 'slug' => 'dual-zone-ac'],
            ['feature_group' => 'Tiện nghi & Nội thất', 'name' => 'Làm mát & sưởi ấm hàng ghế trước', 'slug' => 'ventilated-seats'],
            ['feature_group' => 'Tiện nghi & Nội thất', 'name' => 'Cốp đóng/mở điện rảnh tay (Đá cốp thông minh)', 'slug' => 'hands-free-tailgate'],
            ['feature_group' => 'Tiện nghi & Nội thất', 'name' => 'Đèn viền nội thất Ambient Light 64 màu', 'slug' => 'ambient-lighting'],

            // Công nghệ & Giải trí
            ['feature_group' => 'Công nghệ & Giải trí', 'name' => 'Kết nối Apple CarPlay & Android Auto không dây', 'slug' => 'apple-carplay'],
            ['feature_group' => 'Công nghệ & Giải trí', 'name' => 'Màn hình hiển thị thông tin kính lái HUD', 'slug' => 'head-up-display'],
            ['feature_group' => 'Công nghệ & Giải trí', 'name' => 'Sạc điện thoại không dây chuẩn Qi', 'slug' => 'wireless-charging'],
            ['feature_group' => 'Công nghệ & Giải trí', 'name' => 'Dàn âm thanh vòm cao cấp (Bose / JBL / Burmester / Harman Kardon)', 'slug' => 'premium-audio'],
            ['feature_group' => 'Công nghệ & Giải trí', 'name' => 'Bảng đồng hồ kỹ thuật số Full Digital', 'slug' => 'digital-cockpit'],

            // Vận hành & Ngoại thất
            ['feature_group' => 'Vận hành & Ngoại thất', 'name' => 'Đèn pha thông minh thích ứng Matrix LED / Digital Light', 'slug' => 'matrix-led'],
            ['feature_group' => 'Vận hành & Ngoại thất', 'name' => 'Phanh tay điện tử & Tự động giữ phanh Auto Hold', 'slug' => 'electronic-parking-brake'],
            ['feature_group' => 'Vận hành & Ngoại thất', 'name' => 'Nhiều chế độ lái đa địa hình (Drive Modes)', 'slug' => 'drive-modes'],
            ['feature_group' => 'Vận hành & Ngoại thất', 'name' => 'Hệ thống treo thích ứng / Treo khí nén nâng hạ gầm', 'slug' => 'adaptive-suspension'],
        ];

        foreach ($features as $feature) {
            DB::table('features')->updateOrInsert(
                ['slug' => $feature['slug']],
                [
                    'feature_group_id' => $featureGroupIds[$feature['feature_group']],
                    'name' => $feature['name'],
                ]
            );
        }

        // 3. Specification Attributes
        $attributes = [
            ['code' => 'engine', 'label' => 'Động cơ', 'type' => 'string', 'unit' => null, 'is_filterable' => true, 'sort_order' => 10],
            ['code' => 'horsepower', 'label' => 'Công suất cực đại', 'type' => 'number', 'unit' => 'hp', 'is_filterable' => true, 'sort_order' => 20],
            ['code' => 'torque', 'label' => 'Mô-men xoắn cực đại', 'type' => 'number', 'unit' => 'Nm', 'is_filterable' => true, 'sort_order' => 30],
            ['code' => 'fuel_consumption', 'label' => 'Mức tiêu thụ nhiên liệu', 'type' => 'number', 'unit' => 'L/100km', 'is_filterable' => true, 'sort_order' => 40],
            ['code' => 'seats', 'label' => 'Số chỗ ngồi', 'type' => 'number', 'unit' => 'chỗ', 'is_filterable' => true, 'sort_order' => 50],
            ['code' => 'acceleration', 'label' => 'Tăng tốc 0-100 km/h', 'type' => 'number', 'unit' => 'giây', 'is_filterable' => true, 'sort_order' => 60],
            ['code' => 'sunroof', 'label' => 'Cửa sổ trời', 'type' => 'boolean', 'unit' => null, 'is_filterable' => true, 'sort_order' => 70],
            ['code' => 'seat_material', 'label' => 'Chất liệu ghế', 'type' => 'string', 'unit' => null, 'is_filterable' => false, 'sort_order' => 80],
        ];

        foreach ($attributes as $attribute) {
            DB::table('attributes')->updateOrInsert(['code' => $attribute['code']], $attribute);
        }

        $attributeIds = DB::table('attributes')->pluck('id', 'code');
        $featureIds = DB::table('features')->pluck('id', 'slug');
        $trimIds = DB::table('trims')->pluck('id', 'slug');

        // 4. Mapping Features for all 33 Trims
        $trimFeatureMap = [
            // Toyota
            'vios-e' => ['abs', 'six-airbags', 'apple-carplay', 'parking-sensors'],
            'vios-g' => ['abs', 'six-airbags', 'dual-zone-ac', 'apple-carplay', 'parking-sensors', 'adas'],
            'corolla-cross-18v' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'electronic-parking-brake'],
            'corolla-cross-hybrid' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'wireless-charging', 'electronic-parking-brake'],
            'camry-20q' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'leather-seats', 'dual-zone-ac', 'head-up-display', 'apple-carplay', 'premium-audio', 'electronic-parking-brake', 'wireless-charging'],
            'camry-25hev' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'head-up-display', 'apple-carplay', 'premium-audio', 'electronic-parking-brake', 'wireless-charging', 'ambient-lighting'],

            // Honda
            'civic-g' => ['abs', 'six-airbags', 'adas', 'apple-carplay', 'electronic-parking-brake', 'drive-modes'],
            'civic-rs' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'electronic-parking-brake', 'drive-modes', 'wireless-charging'],
            'city-l' => ['abs', 'six-airbags', 'adas', 'dual-zone-ac', 'apple-carplay', 'parking-sensors'],
            'city-rs' => ['abs', 'six-airbags', 'adas', 'dual-zone-ac', 'apple-carplay', 'parking-sensors', 'drive-modes'],
            'crv-l-2wd' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'electronic-parking-brake', 'wireless-charging'],
            'crv-ehev-rs' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'hands-free-tailgate', 'head-up-display', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'electronic-parking-brake', 'wireless-charging'],

            // Ford
            'ranger-xls' => ['abs', 'six-airbags', 'apple-carplay', 'digital-cockpit', 'parking-sensors'],
            'ranger-wildtrak' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'leather-seats', 'dual-zone-ac', 'apple-carplay', 'digital-cockpit', 'electronic-parking-brake', 'drive-modes', 'wireless-charging', 'matrix-led'],
            'ranger-raptor' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'leather-seats', 'dual-zone-ac', 'apple-carplay', 'digital-cockpit', 'electronic-parking-brake', 'drive-modes', 'wireless-charging', 'matrix-led', 'adaptive-suspension'],
            'everest-titanium-plus' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'digital-cockpit', 'electronic-parking-brake', 'drive-modes', 'wireless-charging', 'matrix-led'],
            'territory-titanium-x' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'digital-cockpit', 'electronic-parking-brake', 'wireless-charging'],

            // Hyundai
            'accent-15at' => ['abs', 'six-airbags', 'adas', 'leather-seats', 'ventilated-seats', 'apple-carplay', 'wireless-charging', 'electronic-parking-brake'],
            'santafe-calligraphy-turbo' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'head-up-display', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'electronic-parking-brake', 'wireless-charging', 'matrix-led'],
            'tucson-16-turbo' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'electronic-parking-brake', 'wireless-charging'],

            // Kia
            'seltos-gtline' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'leather-seats', 'dual-zone-ac', 'apple-carplay', 'digital-cockpit', 'electronic-parking-brake', 'drive-modes', 'wireless-charging'],
            'carnival-signature-7s' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'electronic-parking-brake', 'wireless-charging'],

            // Mazda
            'cx5-20-premium' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'head-up-display', 'apple-carplay', 'premium-audio', 'electronic-parking-brake'],
            'mazda3-premium-sport' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'leather-seats', 'dual-zone-ac', 'head-up-display', 'apple-carplay', 'digital-cockpit', 'electronic-parking-brake'],

            // Mercedes-Benz
            'c200-avantgarde-plus' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'leather-seats', 'dual-zone-ac', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'electronic-parking-brake', 'ambient-lighting', 'wireless-charging'],
            'c300-amg' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'head-up-display', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'matrix-led', 'ambient-lighting', 'electronic-parking-brake', 'wireless-charging'],
            'glc300-4matic' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'head-up-display', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'matrix-led', 'ambient-lighting', 'electronic-parking-brake', 'wireless-charging', 'adaptive-suspension'],

            // BMW
            'bmw-320i-m-sport' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'matrix-led', 'ambient-lighting', 'electronic-parking-brake', 'drive-modes', 'wireless-charging'],
            'bmw-520i-m-sport' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'head-up-display', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'matrix-led', 'ambient-lighting', 'electronic-parking-brake', 'wireless-charging'],

            // Audi
            'audi-a6-45-tfsi' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'matrix-led', 'ambient-lighting', 'electronic-parking-brake', 'drive-modes'],

            // Lexus
            'lexus-rx350-premium' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'head-up-display', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'matrix-led', 'ambient-lighting', 'electronic-parking-brake', 'wireless-charging'],

            // Porsche
            'porsche-macan-base' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'matrix-led', 'electronic-parking-brake', 'drive-modes', 'adaptive-suspension'],

            // Peugeot
            'peugeot-3008-gt' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'adas', 'camera-360', 'panoramic-sunroof', 'leather-seats', 'ventilated-seats', 'dual-zone-ac', 'hands-free-tailgate', 'apple-carplay', 'premium-audio', 'digital-cockpit', 'electronic-parking-brake', 'wireless-charging'],
        ];

        DB::table('trim_feature')->delete();
        foreach ($trimFeatureMap as $trimSlug => $featureSlugs) {
            if (! isset($trimIds[$trimSlug])) {
                continue;
            }

            foreach ($featureSlugs as $featureSlug) {
                if (! isset($featureIds[$featureSlug])) {
                    continue;
                }

                DB::table('trim_feature')->insert([
                    'trim_id' => $trimIds[$trimSlug],
                    'feature_id' => $featureIds[$featureSlug],
                ]);
            }
        }

        // 5. Accurate Specifications (Attributes) for all 33 Trims
        $trimSpecsData = [
            // Toyota
            'vios-e' => [
                'engine' => '1.5L Dual VVT-i (2NR-FE)', 'horsepower' => 106, 'torque' => 140, 'fuel_consumption' => 5.8,
                'seats' => 5, 'acceleration' => 11.5, 'sunroof' => false, 'seat_material' => 'Nỉ cao cấp',
            ],
            'vios-g' => [
                'engine' => '1.5L Dual VVT-i (2NR-FE)', 'horsepower' => 106, 'torque' => 140, 'fuel_consumption' => 5.4,
                'seats' => 5, 'acceleration' => 11.0, 'sunroof' => false, 'seat_material' => 'Da tiêu chuẩn',
            ],
            'corolla-cross-18v' => [
                'engine' => '1.8L DOHC Dual VVT-i', 'horsepower' => 138, 'torque' => 172, 'fuel_consumption' => 7.0,
                'seats' => 5, 'acceleration' => 10.2, 'sunroof' => true, 'seat_material' => 'Da cao cấp',
            ],
            'corolla-cross-hybrid' => [
                'engine' => '1.8L Xăng lai Điện Hybrid', 'horsepower' => 170, 'torque' => 205, 'fuel_consumption' => 4.2,
                'seats' => 5, 'acceleration' => 9.8, 'sunroof' => true, 'seat_material' => 'Da cao cấp',
            ],
            'camry-20q' => [
                'engine' => '2.0L Dynamic Force (M20A-FKS)', 'horsepower' => 170, 'torque' => 206, 'fuel_consumption' => 6.4,
                'seats' => 5, 'acceleration' => 9.2, 'sunroof' => true, 'seat_material' => 'Da cao cấp',
            ],
            'camry-25hev' => [
                'engine' => '2.5L Dynamic Force Hybrid', 'horsepower' => 207, 'torque' => 221, 'fuel_consumption' => 4.4,
                'seats' => 5, 'acceleration' => 8.3, 'sunroof' => true, 'seat_material' => 'Da Nappa cao cấp',
            ],

            // Honda
            'civic-g' => [
                'engine' => '1.5L DOHC VTEC Turbo', 'horsepower' => 176, 'torque' => 240, 'fuel_consumption' => 6.3,
                'seats' => 5, 'acceleration' => 8.5, 'sunroof' => false, 'seat_material' => 'Nỉ cao cấp',
            ],
            'civic-rs' => [
                'engine' => '1.5L DOHC VTEC Turbo', 'horsepower' => 176, 'torque' => 240, 'fuel_consumption' => 6.4,
                'seats' => 5, 'acceleration' => 8.1, 'sunroof' => true, 'seat_material' => 'Da lộn viền chỉ đỏ',
            ],
            'city-l' => [
                'engine' => '1.5L DOHC i-VTEC', 'horsepower' => 119, 'torque' => 145, 'fuel_consumption' => 5.6,
                'seats' => 5, 'acceleration' => 10.5, 'sunroof' => false, 'seat_material' => 'Da cao cấp',
            ],
            'city-rs' => [
                'engine' => '1.5L DOHC i-VTEC', 'horsepower' => 119, 'torque' => 145, 'fuel_consumption' => 5.7,
                'seats' => 5, 'acceleration' => 10.2, 'sunroof' => false, 'seat_material' => 'Da lộn thể thao RS',
            ],
            'crv-l-2wd' => [
                'engine' => '1.5L DOHC VTEC Turbo', 'horsepower' => 188, 'torque' => 240, 'fuel_consumption' => 7.5,
                'seats' => 7, 'acceleration' => 9.3, 'sunroof' => true, 'seat_material' => 'Da cao cấp',
            ],
            'crv-ehev-rs' => [
                'engine' => '2.0L Atkinson Hybrid i-MMD', 'horsepower' => 204, 'torque' => 335, 'fuel_consumption' => 5.2,
                'seats' => 5, 'acceleration' => 8.4, 'sunroof' => true, 'seat_material' => 'Da cao cấp RS',
            ],

            // Ford
            'ranger-xls' => [
                'engine' => '2.0L Single Turbo Diesel', 'horsepower' => 170, 'torque' => 405, 'fuel_consumption' => 7.2,
                'seats' => 5, 'acceleration' => 10.8, 'sunroof' => false, 'seat_material' => 'Nỉ cao cấp',
            ],
            'ranger-wildtrak' => [
                'engine' => '2.0L Bi-Turbo Diesel', 'horsepower' => 210, 'torque' => 500, 'fuel_consumption' => 7.8,
                'seats' => 5, 'acceleration' => 9.5, 'sunroof' => false, 'seat_material' => 'Da Wildtrak cao cấp',
            ],
            'ranger-raptor' => [
                'engine' => '2.0L Bi-Turbo Diesel Raptor', 'horsepower' => 210, 'torque' => 500, 'fuel_consumption' => 8.5,
                'seats' => 5, 'acceleration' => 8.9, 'sunroof' => false, 'seat_material' => 'Da thể thao Raptor',
            ],
            'everest-titanium-plus' => [
                'engine' => '2.0L Bi-Turbo Diesel', 'horsepower' => 210, 'torque' => 500, 'fuel_consumption' => 8.0,
                'seats' => 7, 'acceleration' => 9.8, 'sunroof' => true, 'seat_material' => 'Da cao cấp',
            ],
            'territory-titanium-x' => [
                'engine' => '1.5L EcoBoost Turbo', 'horsepower' => 160, 'torque' => 248, 'fuel_consumption' => 7.0,
                'seats' => 5, 'acceleration' => 9.1, 'sunroof' => true, 'seat_material' => 'Da thông gió cao cấp',
            ],

            // Hyundai
            'accent-15at' => [
                'engine' => '1.5L Smartstream G', 'horsepower' => 115, 'torque' => 144, 'fuel_consumption' => 5.8,
                'seats' => 5, 'acceleration' => 10.4, 'sunroof' => true, 'seat_material' => 'Da cao cấp làm mát',
            ],
            'santafe-calligraphy-turbo' => [
                'engine' => '2.5L Smartstream G-Turbo', 'horsepower' => 281, 'torque' => 422, 'fuel_consumption' => 8.8,
                'seats' => 7, 'acceleration' => 7.8, 'sunroof' => true, 'seat_material' => 'Da Nappa cao cấp',
            ],
            'tucson-16-turbo' => [
                'engine' => '1.6L Smartstream T-GDi', 'horsepower' => 180, 'torque' => 265, 'fuel_consumption' => 7.2,
                'seats' => 5, 'acceleration' => 8.8, 'sunroof' => true, 'seat_material' => 'Da cao cấp sưởi/làm mát',
            ],

            // Kia
            'seltos-gtline' => [
                'engine' => '1.5L Smartstream T-GDi Turbo', 'horsepower' => 158, 'torque' => 253, 'fuel_consumption' => 6.8,
                'seats' => 5, 'acceleration' => 8.9, 'sunroof' => true, 'seat_material' => 'Da thể thao GT-Line',
            ],
            'carnival-signature-7s' => [
                'engine' => '2.2L Smartstream D Diesel', 'horsepower' => 199, 'torque' => 440, 'fuel_consumption' => 7.0,
                'seats' => 7, 'acceleration' => 9.5, 'sunroof' => true, 'seat_material' => 'Da cao cấp VIP thương gia',
            ],

            // Mazda
            'cx5-20-premium' => [
                'engine' => '2.0L SkyActiv-G', 'horsepower' => 154, 'torque' => 200, 'fuel_consumption' => 7.1,
                'seats' => 5, 'acceleration' => 9.6, 'sunroof' => true, 'seat_material' => 'Da Nappa cao cấp',
            ],
            'mazda3-premium-sport' => [
                'engine' => '1.5L SkyActiv-G', 'horsepower' => 110, 'torque' => 146, 'fuel_consumption' => 5.9,
                'seats' => 5, 'acceleration' => 10.5, 'sunroof' => true, 'seat_material' => 'Da cao cấp',
            ],

            // Mercedes-Benz
            'c200-avantgarde-plus' => [
                'engine' => '1.5L Turbo EQ Boost 48V', 'horsepower' => 204, 'torque' => 300, 'fuel_consumption' => 6.5,
                'seats' => 5, 'acceleration' => 7.3, 'sunroof' => false, 'seat_material' => 'Da Artico cao cấp',
            ],
            'c300-amg' => [
                'engine' => '2.0L Turbo Mild Hybrid EQ Boost', 'horsepower' => 258, 'torque' => 400, 'fuel_consumption' => 7.4,
                'seats' => 5, 'acceleration' => 6.0, 'sunroof' => true, 'seat_material' => 'Da thật thể thao AMG',
            ],
            'glc300-4matic' => [
                'engine' => '2.0L Turbo EQ Boost 48V', 'horsepower' => 258, 'torque' => 400, 'fuel_consumption' => 7.9,
                'seats' => 5, 'acceleration' => 6.2, 'sunroof' => true, 'seat_material' => 'Da cao cấp AMG',
            ],

            // BMW
            'bmw-320i-m-sport' => [
                'engine' => '2.0L TwinPower Turbo (B48)', 'horsepower' => 184, 'torque' => 300, 'fuel_consumption' => 6.4,
                'seats' => 5, 'acceleration' => 7.4, 'sunroof' => true, 'seat_material' => 'Da Sensatec & Alcantara',
            ],
            'bmw-520i-m-sport' => [
                'engine' => '2.0L TwinPower Turbo Mild Hybrid', 'horsepower' => 184, 'torque' => 290, 'fuel_consumption' => 6.8,
                'seats' => 5, 'acceleration' => 7.9, 'sunroof' => true, 'seat_material' => 'Da Dakota cao cấp',
            ],

            // Audi
            'audi-a6-45-tfsi' => [
                'engine' => '2.0L TFSI Turbo Mild Hybrid 12V', 'horsepower' => 245, 'torque' => 370, 'fuel_consumption' => 7.1,
                'seats' => 5, 'acceleration' => 6.8, 'sunroof' => true, 'seat_material' => 'Da Milano cao cấp',
            ],

            // Lexus
            'lexus-rx350-premium' => [
                'engine' => '2.4L Turbo Dual VVT-i (T24A-FTS)', 'horsepower' => 275, 'torque' => 430, 'fuel_consumption' => 8.5,
                'seats' => 5, 'acceleration' => 7.6, 'sunroof' => true, 'seat_material' => 'Da Semi-Aniline mềm mại',
            ],

            // Porsche
            'porsche-macan-base' => [
                'engine' => '2.0L Turbo Inline-4 PDK', 'horsepower' => 265, 'torque' => 400, 'fuel_consumption' => 8.8,
                'seats' => 5, 'acceleration' => 6.4, 'sunroof' => true, 'seat_material' => 'Da thể thao cao cấp',
            ],

            // Peugeot
            'peugeot-3008-gt' => [
                'engine' => '1.6L THP Turbo tăng áp', 'horsepower' => 165, 'torque' => 245, 'fuel_consumption' => 6.9,
                'seats' => 5, 'acceleration' => 9.8, 'sunroof' => true, 'seat_material' => 'Da Claudia Habana',
            ],
        ];

        DB::table('trim_attribute_values')->delete();

        foreach ($trimSpecsData as $trimSlug => $specs) {
            if (! isset($trimIds[$trimSlug])) {
                continue;
            }

            $trimId = $trimIds[$trimSlug];

            foreach ($specs as $code => $val) {
                if (! isset($attributeIds[$code])) {
                    continue;
                }

                $attrId = $attributeIds[$code];
                $data = [
                    'trim_id' => $trimId,
                    'attribute_id' => $attrId,
                    'value_string' => null,
                    'value_number' => null,
                    'value_boolean' => null,
                ];

                if (is_string($val)) {
                    $data['value_string'] = $val;
                } elseif (is_numeric($val)) {
                    $data['value_number'] = $val;
                } elseif (is_bool($val)) {
                    $data['value_boolean'] = $val;
                }

                DB::table('trim_attribute_values')->insert($data);
            }
        }
    }
}
