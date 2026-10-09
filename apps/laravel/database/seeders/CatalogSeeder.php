<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    /**
     * Seed makes, models, and trims with rich data and authentic brand logos.
     */
    public function run(): void
    {
        $now = now();

        // 1. Seed Makes (12 top car brands with authentic official logos)
        $makes = [
            [
                'name' => 'Toyota',
                'slug' => 'toyota',
                'logo_path' => 'boxcar/images/brands/toyota.svg',
            ],
            [
                'name' => 'Honda',
                'slug' => 'honda',
                'logo_path' => 'boxcar/images/brands/honda.svg',
            ],
            [
                'name' => 'Ford',
                'slug' => 'ford',
                'logo_path' => 'boxcar/images/resource/brand-3.png',
            ],
            [
                'name' => 'Hyundai',
                'slug' => 'hyundai',
                'logo_path' => 'boxcar/images/brands/hyundai.svg',
            ],
            [
                'name' => 'Kia',
                'slug' => 'kia',
                'logo_path' => 'boxcar/images/brands/kia.svg',
            ],
            [
                'name' => 'Mazda',
                'slug' => 'mazda',
                'logo_path' => 'boxcar/images/brands/mazda.svg',
            ],
            [
                'name' => 'Mercedes-Benz',
                'slug' => 'mercedes-benz',
                'logo_path' => 'boxcar/images/resource/brand-4.png',
            ],
            [
                'name' => 'BMW',
                'slug' => 'bmw',
                'logo_path' => 'boxcar/images/resource/brand-2.png',
            ],
            [
                'name' => 'Audi',
                'slug' => 'audi',
                'logo_path' => 'boxcar/images/resource/brand-1.png',
            ],
            [
                'name' => 'Lexus',
                'slug' => 'lexus',
                'logo_path' => 'boxcar/images/brands/lexus.svg',
            ],
            [
                'name' => 'Porsche',
                'slug' => 'porsche',
                'logo_path' => 'boxcar/images/brands/porsche.svg',
            ],
            [
                'name' => 'Peugeot',
                'slug' => 'peugeot',
                'logo_path' => 'boxcar/images/resource/brand-5.png',
            ],
        ];

        foreach ($makes as $make) {
            DB::table('makes')->updateOrInsert(
                ['slug' => $make['slug']],
                [
                    'name' => $make['name'],
                    'logo_path' => $make['logo_path'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $makeIds = DB::table('makes')->pluck('id', 'slug');

        // 2. Seed Models (Car lines for each make)
        $models = [
            // Toyota
            ['make_slug' => 'toyota', 'name' => 'Vios', 'slug' => 'vios'],
            ['make_slug' => 'toyota', 'name' => 'Corolla Cross', 'slug' => 'corolla-cross'],
            ['make_slug' => 'toyota', 'name' => 'Camry', 'slug' => 'camry'],

            // Honda
            ['make_slug' => 'honda', 'name' => 'Civic', 'slug' => 'civic'],
            ['make_slug' => 'honda', 'name' => 'City', 'slug' => 'city'],
            ['make_slug' => 'honda', 'name' => 'CR-V', 'slug' => 'cr-v'],

            // Ford
            ['make_slug' => 'ford', 'name' => 'Ranger', 'slug' => 'ranger'],
            ['make_slug' => 'ford', 'name' => 'Everest', 'slug' => 'everest'],
            ['make_slug' => 'ford', 'name' => 'Territory', 'slug' => 'territory'],

            // Hyundai
            ['make_slug' => 'hyundai', 'name' => 'Accent', 'slug' => 'accent'],
            ['make_slug' => 'hyundai', 'name' => 'Santa Fe', 'slug' => 'santa-fe'],
            ['make_slug' => 'hyundai', 'name' => 'Tucson', 'slug' => 'tucson'],

            // Kia
            ['make_slug' => 'kia', 'name' => 'Seltos', 'slug' => 'seltos'],
            ['make_slug' => 'kia', 'name' => 'Carnival', 'slug' => 'carnival'],
            ['make_slug' => 'kia', 'name' => 'Sonet', 'slug' => 'sonet'],

            // Mazda
            ['make_slug' => 'mazda', 'name' => 'CX-5', 'slug' => 'cx-5'],
            ['make_slug' => 'mazda', 'name' => 'Mazda 3', 'slug' => 'mazda-3'],
            ['make_slug' => 'mazda', 'name' => 'CX-8', 'slug' => 'cx-8'],

            // Mercedes-Benz
            ['make_slug' => 'mercedes-benz', 'name' => 'C-Class', 'slug' => 'c-class'],
            ['make_slug' => 'mercedes-benz', 'name' => 'GLC', 'slug' => 'glc'],
            ['make_slug' => 'mercedes-benz', 'name' => 'S-Class', 'slug' => 's-class'],

            // BMW
            ['make_slug' => 'bmw', 'name' => '3 Series', 'slug' => '3-series'],
            ['make_slug' => 'bmw', 'name' => '5 Series', 'slug' => '5-series'],
            ['make_slug' => 'bmw', 'name' => 'X5', 'slug' => 'x5'],

            // Audi
            ['make_slug' => 'audi', 'name' => 'A6', 'slug' => 'a6'],
            ['make_slug' => 'audi', 'name' => 'Q5', 'slug' => 'q5'],

            // Lexus
            ['make_slug' => 'lexus', 'name' => 'RX', 'slug' => 'rx'],
            ['make_slug' => 'lexus', 'name' => 'ES', 'slug' => 'es'],

            // Porsche
            ['make_slug' => 'porsche', 'name' => 'Macan', 'slug' => 'macan'],
            ['make_slug' => 'porsche', 'name' => 'Cayenne', 'slug' => 'cayenne'],
            ['make_slug' => 'porsche', 'name' => '911', 'slug' => '911'],

            // Peugeot
            ['make_slug' => 'peugeot', 'name' => '3008', 'slug' => 'peugeot-3008'],
            ['make_slug' => 'peugeot', 'name' => '5008', 'slug' => 'peugeot-5008'],
        ];

        foreach ($models as $model) {
            if (! isset($makeIds[$model['make_slug']])) {
                continue;
            }

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

        $modelIds = DB::table('models')->pluck('id', 'slug');

        // 3. Seed Trims (Versions with realistic MSRP in VND and full Vietnamese accents)
        $trims = [
            // Toyota Vios
            [
                'model_slug' => 'vios',
                'name' => 'Vios 1.5E MT',
                'slug' => 'vios-e',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 458000000,
                'description' => 'Bản sedan số sàn tiêu chuẩn, tiết kiệm nhiên liệu, chi phí bảo dưỡng hợp lý cho gia đình và chạy dịch vụ.',
            ],
            [
                'model_slug' => 'vios',
                'name' => 'Vios 1.5G CVT',
                'slug' => 'vios-g',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 545000000,
                'description' => 'Phiên bản cao cấp với hộp số tự động vô cấp CVT, đèn LED Projector và 7 túi khí an toàn.',
            ],

            // Toyota Corolla Cross
            [
                'model_slug' => 'corolla-cross',
                'name' => 'Corolla Cross 1.8V',
                'slug' => 'corolla-cross-18v',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 820000000,
                'description' => 'Phiên bản máy xăng cao cấp trang bị gói hỗ trợ an toàn chủ động Toyota Safety Sense.',
            ],
            [
                'model_slug' => 'corolla-cross',
                'name' => 'Corolla Cross Hybrid',
                'slug' => 'corolla-cross-hybrid',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 905000000,
                'description' => 'Phiên bản động cơ xăng lai điện Hybrid êm ái, mức tiêu thụ nhiên liệu chỉ khoảng 4.2L/100km trong đô thị.',
            ],

            // Toyota Camry
            [
                'model_slug' => 'camry',
                'name' => 'Camry 2.0Q',
                'slug' => 'camry-20q',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1220000000,
                'description' => 'Sedan hạng D lịch lãm với hàng ghế sau chỉnh điện chuẩn doanh nhân và dàn âm thanh JBL 9 loa.',
            ],
            [
                'model_slug' => 'camry',
                'name' => 'Camry 2.5HEV',
                'slug' => 'camry-25hev',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1495000000,
                'description' => 'Phiên bản đầu bảng công nghệ Hybrid đỉnh cao, vận hành tĩnh lặng và đẳng cấp thượng lưu.',
            ],

            // Honda Civic
            [
                'model_slug' => 'civic',
                'name' => 'Civic G',
                'slug' => 'civic-g',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 789000000,
                'description' => 'Sedan thể thao thế hệ thứ 11, động cơ 1.5 VTEC Turbo mạnh mẽ cùng hệ thống an toàn Honda SENSING.',
            ],
            [
                'model_slug' => 'civic',
                'name' => 'Civic RS',
                'slug' => 'civic-rs',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 870000000,
                'description' => 'Phiên bản thể thao RS cao cấp nhất với cánh lướt gió đen bóng, ống xả kép và ghế da lộn viền chỉ đỏ.',
            ],

            // Honda City
            [
                'model_slug' => 'city',
                'name' => 'City L',
                'slug' => 'city-l',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 559000000,
                'description' => 'Bản sedan hạng B tiện nghi, trang bị phanh đĩa 4 bánh, khởi động từ xa và hệ thống Honda SENSING tiêu chuẩn.',
            ],
            [
                'model_slug' => 'city',
                'name' => 'City RS',
                'slug' => 'city-rs',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 609000000,
                'description' => 'Phiên bản RS phong cách đua thể thao, mâm hợp kim 16 inch phay xước, lẫy chuyển số trên vô lăng.',
            ],

            // Honda CR-V
            [
                'model_slug' => 'cr-v',
                'name' => 'CR-V L 2WD',
                'slug' => 'crv-l-2wd',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1109000000,
                'description' => 'Crossover 7 chỗ đa dụng thế hệ mới, trang bị cửa sổ trời toàn cảnh panorama và cốp điện rảnh tay.',
            ],
            [
                'model_slug' => 'cr-v',
                'name' => 'CR-V e:HEV RS',
                'slug' => 'crv-ehev-rs',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1259000000,
                'description' => 'Bản Hybrid thể thao 5 chỗ nhập khẩu nguyên chiếc từ Thái Lan, công suất kết hợp 204 mã lực cực bốc.',
            ],

            // Ford Ranger
            [
                'model_slug' => 'ranger',
                'name' => 'Ranger XLS 4x2 AT',
                'slug' => 'ranger-xls',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 707000000,
                'description' => 'Mẫu bán tải 1 cầu số tự động thông dụng, màn hình trung tâm 10 inch với hệ điều hành SYNC 4.',
            ],
            [
                'model_slug' => 'ranger',
                'name' => 'Ranger Wildtrak 4x4',
                'slug' => 'ranger-wildtrak',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 979000000,
                'description' => 'Bán tải chuyên dụng hai cầu động cơ 2.0L Bi-Turbo, hộp số tự động 10 cấp và 6 chế độ lái địa hình.',
            ],
            [
                'model_slug' => 'ranger',
                'name' => 'Ranger Raptor',
                'slug' => 'ranger-raptor',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1299000000,
                'description' => 'Siêu bán tải hiệu năng cao trang bị bộ giảm chấn FOX Live Valve 2.5 inch và chế độ lái Baja chạy sa mạc.',
            ],

            // Ford Everest
            [
                'model_slug' => 'everest',
                'name' => 'Everest Titanium+ 4x4',
                'slug' => 'everest-titanium-plus',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1468000000,
                'description' => 'SUV 7 chỗ cao cấp trang bị động cơ Bi-Turbo, dẫn động 4 bánh toàn thời gian và camera toàn cảnh 360 độ.',
            ],

            // Ford Territory
            [
                'model_slug' => 'territory',
                'name' => 'Territory Titanium X',
                'slug' => 'territory-titanium-x',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 929000000,
                'description' => 'C-SUV trẻ trung với khoang nội thất rộng hàng đầu phân khúc, cụm màn hình kép 12 inch và làm mát ghế.',
            ],

            // Hyundai Accent
            [
                'model_slug' => 'accent',
                'name' => 'Accent 1.5 AT Đặc Biệt',
                'slug' => 'accent-15at',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 569000000,
                'description' => 'Sedan hạng B thế hệ hoàn toàn mới với kích thước vượt trội, động cơ Smartstream 1.5L và gói SmartSense.',
            ],

            // Hyundai Santa Fe
            [
                'model_slug' => 'santa-fe',
                'name' => 'Santa Fe Calligraphy 2.5 Turbo',
                'slug' => 'santafe-calligraphy-turbo',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1365000000,
                'description' => 'SUV cỡ D đầu bảng phong cách khối hộp ấn tượng, động cơ 281 mã lực cùng ghế da Nappa cao cấp.',
            ],

            // Hyundai Tucson
            [
                'model_slug' => 'tucson',
                'name' => 'Tucson 1.6T Turbo HTRAC',
                'slug' => 'tucson-16-turbo',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 989000000,
                'description' => 'Crossover thiết kế đèn cánh chim Parametric Jewel, dẫn động 4 bánh HTRAC và hệ thống âm thanh 8 loa Bose.',
            ],

            // Kia Seltos
            [
                'model_slug' => 'seltos',
                'name' => 'Seltos 1.5 Turbo GT-Line',
                'slug' => 'seltos-gtline',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 799000000,
                'description' => 'B-SUV phiên bản thể thao GT-Line với động cơ Turbo 158 mã lực, phanh tay điện tử và màn hình giải trí 10.25 inch.',
            ],

            // Kia Carnival
            [
                'model_slug' => 'carnival',
                'name' => 'Carnival 2.2D Signature 7S',
                'slug' => 'carnival-signature-7s',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1439000000,
                'description' => 'Mẫu MPV thương gia cao cấp với hàng ghế thứ 2 VIP chỉnh điện, đệm đỡ bắp chân và cửa trượt điện thông minh.',
            ],

            // Mazda CX-5
            [
                'model_slug' => 'cx-5',
                'name' => 'CX-5 2.0L Premium Exclusive',
                'slug' => 'cx5-20-premium',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 869000000,
                'description' => 'SUV 5 chỗ thiết kế KODO tinh tế, trang bị gói công nghệ an toàn cao cấp i-Activesense và 10 loa Bose.',
            ],

            // Mazda 3
            [
                'model_slug' => 'mazda-3',
                'name' => 'Mazda 3 1.5L Premium Sport',
                'slug' => 'mazda3-premium-sport',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 739000000,
                'description' => 'Sedan thể thao quyến rũ với cụm màn hình hiển thị kính lái HUD và nội thất da cao cấp.',
            ],

            // Mercedes-Benz C-Class
            [
                'model_slug' => 'c-class',
                'name' => 'C 200 Avantgarde Plus',
                'slug' => 'c200-avantgarde-plus',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1849000000,
                'description' => 'Sedan hạng sang cỡ nhỏ với thiết kế như tiểu S-Class, màn hình trung tâm đặt dọc 11.9 inch và đèn viền 64 màu.',
            ],
            [
                'model_slug' => 'c-class',
                'name' => 'C 300 AMG',
                'slug' => 'c300-amg',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 2099000000,
                'description' => 'Phiên bản thể thao AMG Line với công suất 258 mã lực, hệ thống đèn pha Digital Light tối tân nhất.',
            ],

            // Mercedes-Benz GLC
            [
                'model_slug' => 'glc',
                'name' => 'GLC 300 4MATIC',
                'slug' => 'glc300-4matic',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 2799000000,
                'description' => 'SUV hạng sang bán chạy nhất Việt Nam, động cơ Mild Hybrid 48V và hệ thống dẫn động 4 bánh toàn thời gian.',
            ],

            // BMW 3 Series
            [
                'model_slug' => '3-series',
                'name' => '320i M Sport',
                'slug' => 'bmw-320i-m-sport',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1689000000,
                'description' => 'Sedan thể thao tiêu chuẩn vàng về cảm giác lái, gói trang bị M Sport và cụm màn hình cong BMW Curved Display.',
            ],

            // BMW 5 Series
            [
                'model_slug' => '5-series',
                'name' => '520i M Sport',
                'slug' => 'bmw-520i-m-sport',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 2399000000,
                'description' => 'Sedan hạng sang cỡ trung phong thái lịch lãm, cách âm tĩnh lặng và hệ thống âm thanh vòm Harman Kardon.',
            ],

            // Audi A6
            [
                'model_slug' => 'a6',
                'name' => 'A6 45 TFSI Quattro',
                'slug' => 'audi-a6-45-tfsi',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 2450000000,
                'description' => 'Sedan sang trọng từ Đức với hệ dẫn động 4 bánh toàn thời gian Quattro và khoang lái kỹ thuật số Virtual Cockpit.',
            ],

            // Lexus RX
            [
                'model_slug' => 'rx',
                'name' => 'RX 350 Premium',
                'slug' => 'lexus-rx350-premium',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 3430000000,
                'description' => 'SUV hạng sang êm ái hàng đầu, động cơ xăng tăng áp 2.4L mới cùng độ bền bỉ và giữ giá huyền thoại.',
            ],

            // Porsche Macan
            [
                'model_slug' => 'macan',
                'name' => 'Macan Tiêu Chuẩn',
                'slug' => 'porsche-macan-base',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 3350000000,
                'description' => 'SUV thể thao sở hữu DNA xe đua thuần khiết từ Stuttgart, hộp số ly hợp kép PDK 7 cấp trứ danh.',
            ],

            // Peugeot 3008
            [
                'model_slug' => 'peugeot-3008',
                'name' => '3008 GT',
                'slug' => 'peugeot-3008-gt',
                'year_from' => 2024,
                'year_to' => null,
                'msrp' => 1099000000,
                'description' => 'Thiết kế i-Cockpit độc đáo từ nước Pháp với vô lăng vát hai đáy thể thao và ghế bọc da Claudia cao cấp.',
            ],
        ];

        foreach ($trims as $trim) {
            if (! isset($modelIds[$trim['model_slug']])) {
                continue;
            }

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
