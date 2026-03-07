<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesAndAttributesSeeder extends Seeder
{
    /**
     * Seed feature catalogs and trim attribute values.
     */
    public function run(): void
    {
        $featureGroups = [
            ['name' => 'An toàn', 'sort_order' => 10],
            ['name' => 'Tiện nghi', 'sort_order' => 20],
            ['name' => 'Công nghệ', 'sort_order' => 30],
        ];

        foreach ($featureGroups as $group) {
            DB::table('feature_groups')->updateOrInsert(['name' => $group['name']], $group);
        }

        $featureGroupIds = DB::table('feature_groups')
            ->whereIn('name', ['An toàn', 'Tiện nghi', 'Công nghệ'])
            ->pluck('id', 'name');

        $features = [
            ['feature_group' => 'An toàn', 'name' => 'ABS', 'slug' => 'abs'],
            ['feature_group' => 'An toàn', 'name' => '6 túi khí', 'slug' => 'six-airbags'],
            ['feature_group' => 'An toàn', 'name' => 'Blind Spot Monitoring', 'slug' => 'blind-spot-monitoring'],
            ['feature_group' => 'Tiện nghi', 'name' => 'Ghế da', 'slug' => 'leather-seats'],
            ['feature_group' => 'Tiện nghi', 'name' => 'Cửa sổ trời toàn cảnh', 'slug' => 'panoramic-sunroof'],
            ['feature_group' => 'Tiện nghi', 'name' => 'Điều hòa Dual-zone', 'slug' => 'dual-zone-ac'],
            ['feature_group' => 'Công nghệ', 'name' => 'Apple CarPlay', 'slug' => 'apple-carplay'],
            ['feature_group' => 'Công nghệ', 'name' => 'Android Auto', 'slug' => 'android-auto'],
            ['feature_group' => 'Công nghệ', 'name' => 'Adaptive Cruise Control', 'slug' => 'adaptive-cruise-control'],
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

        $trimIds = DB::table('trims')
            ->whereIn('slug', ['vios-e', 'vios-g', 'corolla-cross-hybrid', 'civic-rs', 'city-rs', 'ranger-wildtrak'])
            ->pluck('id', 'slug');

        $featureIds = DB::table('features')
            ->whereIn('slug', array_column($features, 'slug'))
            ->pluck('id', 'slug');

        $trimFeatureMap = [
            'vios-e' => ['abs', 'six-airbags', 'apple-carplay'],
            'vios-g' => ['abs', 'six-airbags', 'dual-zone-ac', 'apple-carplay', 'android-auto'],
            'corolla-cross-hybrid' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'leather-seats', 'adaptive-cruise-control'],
            'civic-rs' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'leather-seats', 'panoramic-sunroof', 'apple-carplay', 'android-auto'],
            'city-rs' => ['abs', 'six-airbags', 'dual-zone-ac', 'apple-carplay', 'android-auto'],
            'ranger-wildtrak' => ['abs', 'six-airbags', 'blind-spot-monitoring', 'leather-seats', 'adaptive-cruise-control'],
        ];

        DB::table('trim_feature')->delete();
        foreach ($trimFeatureMap as $trimSlug => $featureSlugs) {
            foreach ($featureSlugs as $featureSlug) {
                DB::table('trim_feature')->insert([
                    'trim_id' => $trimIds[$trimSlug],
                    'feature_id' => $featureIds[$featureSlug],
                ]);
            }
        }

        $attributes = [
            ['code' => 'engine', 'label' => 'Động cơ', 'type' => 'string', 'unit' => null, 'is_filterable' => true, 'sort_order' => 10],
            ['code' => 'horsepower', 'label' => 'Công suất', 'type' => 'number', 'unit' => 'hp', 'is_filterable' => true, 'sort_order' => 20],
            ['code' => 'torque', 'label' => 'Mô-men xoắn', 'type' => 'number', 'unit' => 'Nm', 'is_filterable' => true, 'sort_order' => 30],
            ['code' => 'fuel_consumption', 'label' => 'Mức tiêu thụ nhiên liệu', 'type' => 'number', 'unit' => 'L/100km', 'is_filterable' => true, 'sort_order' => 40],
            ['code' => 'sunroof', 'label' => 'Cửa sổ trời', 'type' => 'boolean', 'unit' => null, 'is_filterable' => true, 'sort_order' => 50],
            ['code' => 'seat_material', 'label' => 'Chất liệu ghế', 'type' => 'string', 'unit' => null, 'is_filterable' => false, 'sort_order' => 60],
        ];

        foreach ($attributes as $attribute) {
            DB::table('attributes')->updateOrInsert(['code' => $attribute['code']], $attribute);
        }

        $attributeIds = DB::table('attributes')
            ->whereIn('code', array_column($attributes, 'code'))
            ->pluck('id', 'code');

        $trimAttributeValues = [
            ['trim' => 'civic-rs', 'code' => 'engine', 'value_string' => '1.5L Turbo'],
            ['trim' => 'civic-rs', 'code' => 'horsepower', 'value_number' => 176],
            ['trim' => 'civic-rs', 'code' => 'torque', 'value_number' => 240],
            ['trim' => 'civic-rs', 'code' => 'fuel_consumption', 'value_number' => 6.4],
            ['trim' => 'civic-rs', 'code' => 'sunroof', 'value_boolean' => true],
            ['trim' => 'civic-rs', 'code' => 'seat_material', 'value_string' => 'Da'],

            ['trim' => 'corolla-cross-hybrid', 'code' => 'engine', 'value_string' => '1.8L Hybrid'],
            ['trim' => 'corolla-cross-hybrid', 'code' => 'horsepower', 'value_number' => 170],
            ['trim' => 'corolla-cross-hybrid', 'code' => 'torque', 'value_number' => 205],
            ['trim' => 'corolla-cross-hybrid', 'code' => 'fuel_consumption', 'value_number' => 4.2],
            ['trim' => 'corolla-cross-hybrid', 'code' => 'sunroof', 'value_boolean' => false],
            ['trim' => 'corolla-cross-hybrid', 'code' => 'seat_material', 'value_string' => 'Da'],

            ['trim' => 'ranger-wildtrak', 'code' => 'engine', 'value_string' => '2.0L Bi-Turbo Diesel'],
            ['trim' => 'ranger-wildtrak', 'code' => 'horsepower', 'value_number' => 210],
            ['trim' => 'ranger-wildtrak', 'code' => 'torque', 'value_number' => 500],
            ['trim' => 'ranger-wildtrak', 'code' => 'fuel_consumption', 'value_number' => 7.8],
            ['trim' => 'ranger-wildtrak', 'code' => 'sunroof', 'value_boolean' => false],
            ['trim' => 'ranger-wildtrak', 'code' => 'seat_material', 'value_string' => 'Da'],
        ];

        DB::table('trim_attribute_values')->delete();
        foreach ($trimAttributeValues as $row) {
            DB::table('trim_attribute_values')->insert([
                'trim_id' => $trimIds[$row['trim']],
                'attribute_id' => $attributeIds[$row['code']],
                'value_string' => $row['value_string'] ?? null,
                'value_number' => $row['value_number'] ?? null,
                'value_boolean' => $row['value_boolean'] ?? null,
            ]);
        }
    }
}
