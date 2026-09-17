<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmSalesSeeder extends Seeder
{
    /**
     * Seed realistic CRM leads, notes, appointments, sales, and reviews data.
     */
    public function run(): void
    {
        $now = now();
        $today = today();

        $users = DB::table('users')->pluck('id', 'email');
        $trims = DB::table('trims')->pluck('id', 'slug');
        $units = DB::table('car_units')->pluck('id', 'stock_code');

        // Staff members
        $staff1 = $users['staff@showroom.test'] ?? null;
        $staff2 = $users['hoang.sales@showroom.test'] ?? $staff1;
        $staff3 = $users['linh.sales@showroom.test'] ?? $staff1;

        DB::table('appointments')->delete();
        DB::table('lead_notes')->delete();
        DB::table('leads')->delete();
        DB::table('sales')->delete();
        DB::table('trim_reviews')->delete();

        // =========================================================================
        // 1. SEED LEADS (20 LEADS)
        // =========================================================================
        $leadDefinitions = [
            // --- STAGE: NEW (4 leads) ---
            [
                'key' => 'lead_vios_new',
                'user_id' => null,
                'car_unit_id' => $units['NEW-VIOS-001'] ?? null,
                'trim_id' => $trims['vios-g'] ?? null,
                'assigned_to' => null,
                'source' => 'unit_detail',
                'name' => 'Đặng Thùy Trang',
                'phone' => '0911888999',
                'email' => 'trang.dang@gmail.com',
                'message' => 'Báo giá lăn bánh xe Vios G tại TP.HCM và các khuyến mãi tháng này.',
                'status' => 'new',
                'utm_source' => 'google',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'vios-promotion-2026',
                'created_at' => $now->copy()->subHours(3),
                'updated_at' => $now->copy()->subHours(3),
            ],
            [
                'key' => 'lead_carnival_new',
                'user_id' => null,
                'car_unit_id' => $units['NEW-CARNIVAL-001'] ?? null,
                'trim_id' => $trims['carnival-signature-7s'] ?? null,
                'assigned_to' => $staff1,
                'source' => 'contact',
                'name' => 'Vũ Minh Khang',
                'phone' => '0977223344',
                'email' => 'khang.vu@outlook.com',
                'message' => 'Cần xe 7 chỗ Kia Carnival Signature máy dầu giao trước rằm tháng này.',
                'status' => 'new',
                'utm_source' => 'facebook',
                'utm_medium' => 'lead_form',
                'utm_campaign' => 'carnival-family',
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(6),
            ],
            [
                'key' => 'lead_tuan_camry',
                'user_id' => $users['tuan.nguyen@gmail.com'] ?? null,
                'car_unit_id' => $units['NEW-CAMRY-001'] ?? null,
                'trim_id' => $trims['camry-25hev'] ?? null,
                'assigned_to' => $staff2,
                'source' => 'unit_detail',
                'name' => 'Nguyễn Anh Tuấn',
                'phone' => '0912345678',
                'email' => 'tuan.nguyen@gmail.com',
                'message' => 'Xin chào showroom, chiếc Camry 2.5 HEV màu đen này còn sẵn xe giao ngay trong tuần không?',
                'status' => 'new',
                'utm_source' => 'google',
                'utm_medium' => 'organic',
                'utm_campaign' => null,
                'created_at' => $now->copy()->subHours(10),
                'updated_at' => $now->copy()->subHours(10),
            ],
            [
                'key' => 'lead_city_minh',
                'user_id' => null,
                'car_unit_id' => $units['CPO-CITY-001'] ?? null,
                'trim_id' => $trims['city-rs'] ?? null,
                'assigned_to' => $staff1,
                'source' => 'finance',
                'name' => 'Trần Quang Minh',
                'phone' => '0900000104',
                'email' => 'minh.tran@example.com',
                'message' => 'Tôi cần ước tính phê duyệt trước cho kỳ hạn vay 60 tháng xe City RS.',
                'status' => 'new',
                'utm_source' => 'zalo',
                'utm_medium' => 'chat',
                'utm_campaign' => 'finance-calc',
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],

            // --- STAGE: CONSULTING (contacted / qualified) (5 leads) ---
            [
                'key' => 'lead_john_civic',
                'user_id' => $users['john@example.com'] ?? null,
                'car_unit_id' => $units['NEW-CIVIC-001'] ?? null,
                'trim_id' => $trims['civic-rs'] ?? null,
                'assigned_to' => $staff1,
                'source' => 'unit_detail',
                'name' => 'John Buyer',
                'phone' => '0900000003',
                'email' => 'john@example.com',
                'message' => 'Tôi cần báo giá lăn bánh và bảng tính trả góp cho chiếc Civic RS này.',
                'status' => 'qualified',
                'utm_source' => 'google',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'civic-rs-search',
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'key' => 'lead_huong_cx5',
                'user_id' => $users['huong.le@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-CX5-001'] ?? null,
                'trim_id' => $trims['cx5-20-premium'] ?? null,
                'assigned_to' => $staff3,
                'source' => 'finance',
                'name' => 'Lê Thu Hương',
                'phone' => '0983222333',
                'email' => 'huong.le@gmail.com',
                'message' => 'Tôi đang cân nhắc mua xe CX-5 trả góp hoặc mua thẳng, cần tư vấn thêm ưu đãi.',
                'status' => 'qualified',
                'utm_source' => 'facebook',
                'utm_medium' => 'social',
                'utm_campaign' => 'cx5-summer-deal',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'key' => 'lead_thao_accent',
                'user_id' => $users['thao.pham@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-ACCENT-001'] ?? null,
                'trim_id' => $trims['accent-15at'] ?? null,
                'assigned_to' => $staff3,
                'source' => 'trade_in',
                'name' => 'Phạm Phương Thảo',
                'phone' => '0978111222',
                'email' => 'thao.pham@gmail.com',
                'message' => 'Tôi đang đi xe Grand i10 2018, muốn thu cũ đổi mới lên chiếc Accent này.',
                'status' => 'contacted',
                'utm_source' => 'website',
                'utm_medium' => 'trade_in_form',
                'utm_campaign' => null,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subHours(18),
            ],
            [
                'key' => 'lead_bao_c300',
                'user_id' => null,
                'car_unit_id' => $units['NEW-C300-001'] ?? null,
                'trim_id' => $trims['c300-amg'] ?? null,
                'assigned_to' => $staff2,
                'source' => 'unit_detail',
                'name' => 'Lâm Gia Bảo',
                'phone' => '0933112233',
                'email' => 'giabao@gmail.com',
                'message' => 'Tôi muốn hỏi thời gian giao xe Mercedes C300 AMG bản mới nhất.',
                'status' => 'qualified',
                'utm_source' => 'google',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'mercedes-c300-brand',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'key' => 'lead_crv_general',
                'user_id' => null,
                'car_unit_id' => null,
                'trim_id' => $trims['crv-ehev-rs'] ?? null,
                'assigned_to' => $staff1,
                'source' => 'contact',
                'name' => 'Trịnh Quốc Hùng',
                'phone' => '0919224466',
                'email' => 'hung.trinh@gmail.com',
                'message' => 'Cần tư vấn thông số kỹ thuật và độ tiêu hao nhiên liệu bản Honda CR-V Hybrid.',
                'status' => 'contacted',
                'utm_source' => 'youtube',
                'utm_medium' => 'video_review',
                'utm_campaign' => 'crv-hybrid-review',
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(2),
            ],

            // --- STAGE: NEGOTIATING / BOOKED (Lái thử & Thương thảo) (6 leads) ---
            [
                'key' => 'lead_anh_cross',
                'user_id' => null,
                'car_unit_id' => $units['USED-CROSS-001'] ?? null,
                'trim_id' => $trims['corolla-cross-hybrid'] ?? null,
                'assigned_to' => $staff1,
                'source' => 'trim_page',
                'name' => 'Nguyễn Tuấn Anh',
                'phone' => '0900000102',
                'email' => 'anh.nguyen@example.com',
                'message' => 'Tôi muốn đặt lịch test drive chiếc Corolla Cross Hybrid trong tuần này.',
                'status' => 'booked',
                'utm_source' => 'facebook',
                'utm_medium' => 'social',
                'utm_campaign' => 'hybrid-awareness',
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'key' => 'lead_mai_city',
                'user_id' => $users['mai.vu@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-CITY-001'] ?? null,
                'trim_id' => $trims['city-rs'] ?? null,
                'assigned_to' => $staff3,
                'source' => 'unit_detail',
                'name' => 'Vũ Tuyết Mai',
                'phone' => '0965999000',
                'email' => 'mai.vu@gmail.com',
                'message' => 'Tôi muốn lái thử chiếc City RS lướt này vào buổi sáng hôm nay.',
                'status' => 'booked',
                'utm_source' => 'google',
                'utm_medium' => 'organic',
                'utm_campaign' => null,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'key' => 'lead_dat_camry',
                'user_id' => $users['dat.do@gmail.com'] ?? null,
                'car_unit_id' => $units['NEW-CAMRY-001'] ?? null,
                'trim_id' => $trims['camry-25hev'] ?? null,
                'assigned_to' => $staff2,
                'source' => 'unit_detail',
                'name' => 'Đỗ Thành Đạt',
                'phone' => '0942888111',
                'email' => 'dat.do@gmail.com',
                'message' => 'Đặt lịch xem xe và lái thử Camry Hybrid chiều hôm nay.',
                'status' => 'booked',
                'utm_source' => 'tiktok',
                'utm_medium' => 'short_video',
                'utm_campaign' => 'camry-vip',
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subHours(4),
            ],
            [
                'key' => 'lead_dung_everest',
                'user_id' => $users['dung.hoang@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-EVEREST-001'] ?? null,
                'trim_id' => $trims['everest-titanium-plus'] ?? null,
                'assigned_to' => $staff1,
                'source' => 'unit_detail',
                'name' => 'Hoàng Trung Dũng',
                'phone' => '0936777888',
                'email' => 'dung.hoang@gmail.com',
                'message' => 'Gia đình tôi cần xe 7 chỗ gầm cao, đặt lịch xem xe Everest Titanium+ ngày mai.',
                'status' => 'booked',
                'utm_source' => 'facebook',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'everest-suv-deal',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subHours(12),
            ],
            [
                'key' => 'lead_quang_mazda3',
                'user_id' => $users['quang.tran@gmail.com'] ?? null,
                'car_unit_id' => $units['USED-MAZDA3-001'] ?? null,
                'trim_id' => $trims['mazda3-premium-sport'] ?? null,
                'assigned_to' => $staff2,
                'source' => 'unit_detail',
                'name' => 'Trần Đình Quang',
                'phone' => '0904555666',
                'email' => 'quang.tran@gmail.com',
                'message' => 'Chiếc Mazda 3 lướt màu đỏ này đã kiểm tra bao nhiêu hạng mục rồi showroom? Đặt lịch xem xe.',
                'status' => 'booked',
                'utm_source' => 'google',
                'utm_medium' => 'organic',
                'utm_campaign' => null,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDay(),
            ],
            [
                'key' => 'lead_yen_peugeot',
                'user_id' => $users['yen.bui@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-PEUGEOT-001'] ?? null,
                'trim_id' => $trims['peugeot-3008-gt'] ?? null,
                'assigned_to' => $staff3,
                'source' => 'contact',
                'name' => 'Bùi Hải Yến',
                'phone' => '0925333444',
                'email' => 'yen.bui@gmail.com',
                'message' => 'Tôi yêu thích thiết kế xe Pháp, muốn đặt lịch lái thử Peugeot 3008 GT.',
                'status' => 'booked',
                'utm_source' => 'facebook',
                'utm_medium' => 'social',
                'utm_campaign' => 'peugeot-french-luxury',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDay(),
            ],

            // --- STAGE: CLOSED (Chốt giao dịch thành công) (3 leads) ---
            [
                'key' => 'lead_jane_ranger',
                'user_id' => $users['jane@example.com'] ?? null,
                'car_unit_id' => $units['USED-RANGER-001'] ?? null,
                'trim_id' => $trims['ranger-wildtrak'] ?? null,
                'assigned_to' => $staff1,
                'source' => 'contact',
                'name' => 'Jane Buyer',
                'phone' => '0900000004',
                'email' => 'jane@example.com',
                'message' => 'Tôi quan tâm một mẫu bán tải Ranger Wildtrak cho nhu cầu đi lại gia đình và công việc.',
                'status' => 'closed',
                'utm_source' => null,
                'utm_medium' => null,
                'utm_campaign' => null,
                'created_at' => $now->copy()->subDays(14),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'key' => 'lead_david_macan',
                'user_id' => $users['david.miller@example.com'] ?? null,
                'car_unit_id' => $units['NEW-MACAN-001'] ?? null,
                'trim_id' => $trims['porsche-macan-base'] ?? null,
                'assigned_to' => $staff2,
                'source' => 'unit_detail',
                'name' => 'David Miller',
                'phone' => '0909123456',
                'email' => 'david.miller@example.com',
                'message' => 'Interested in purchasing the Porsche Macan available in your showroom.',
                'status' => 'closed',
                'utm_source' => 'google',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'porsche-macan-hanoi',
                'created_at' => $now->copy()->subDays(18),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'key' => 'lead_trang_vios_closed',
                'user_id' => null,
                'car_unit_id' => $units['NEW-VIOS-001'] ?? null,
                'trim_id' => $trims['vios-g'] ?? null,
                'assigned_to' => $staff3,
                'source' => 'trade_in',
                'name' => 'Hoàng Thu Trang',
                'phone' => '0988665544',
                'email' => 'trang.hoang@gmail.com',
                'message' => 'Khách đến showroom đổi xe i10 lên Vios G mới và thanh toán phần chênh lệch.',
                'status' => 'closed',
                'utm_source' => 'referral',
                'utm_medium' => 'customer_referral',
                'utm_campaign' => null,
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(3),
            ],

            // --- STAGE: LOST (Đã hủy / Thất bại) (2 leads) ---
            [
                'key' => 'lead_tuan_lost',
                'user_id' => null,
                'car_unit_id' => $units['USED-CROSS-001'] ?? null,
                'trim_id' => $trims['corolla-cross-hybrid'] ?? null,
                'assigned_to' => $staff1,
                'source' => 'contact',
                'name' => 'Lê Quốc Tuấn',
                'phone' => '0908112244',
                'email' => 'tuan.le@yahoo.com',
                'message' => 'Tìm hiểu dòng xe Corolla Cross Hybrid.',
                'status' => 'lost',
                'utm_source' => 'google',
                'utm_medium' => 'organic',
                'utm_campaign' => null,
                'created_at' => $now->copy()->subDays(12),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'key' => 'lead_nam_lost',
                'user_id' => null,
                'car_unit_id' => $units['NEW-SANTAFE-001'] ?? null,
                'trim_id' => $trims['santafe-calligraphy-turbo'] ?? null,
                'assigned_to' => $staff2,
                'source' => 'finance',
                'name' => 'Phan Đình Nam',
                'phone' => '0915667788',
                'email' => null,
                'message' => 'Tư vấn vay mua Santa Fe Calligraphy trả góp 85%.',
                'status' => 'lost',
                'utm_source' => 'facebook',
                'utm_medium' => 'lead_ad',
                'utm_campaign' => 'santafe-finance',
                'created_at' => $now->copy()->subDays(9),
                'updated_at' => $now->copy()->subDays(4),
            ],
        ];

        $leadIds = [];
        foreach ($leadDefinitions as $def) {
            $key = $def['key'];
            unset($def['key']);
            $leadIds[$key] = DB::table('leads')->insertGetId($def);
        }

        // =========================================================================
        // 2. SEED LEAD NOTES (35+ ACTIVITY LOGS)
        // =========================================================================
        $notes = [
            // Lead John Civic
            [
                'lead_id' => $leadIds['lead_john_civic'],
                'created_by' => $staff1,
                'note' => 'Đã gọi điện trao đổi ban đầu. Khách hỏi chi tiết về gói vay 80% ngân hàng Vietcombank.',
                'created_at' => $now->copy()->subDays(3),
            ],
            [
                'lead_id' => $leadIds['lead_john_civic'],
                'created_by' => $staff1,
                'note' => 'Đã gửi file PDF dự toán chi phí lăn bánh và bảng tính trả trước tại Hà Nội.',
                'created_at' => $now->copy()->subDays(2),
            ],
            [
                'lead_id' => $leadIds['lead_john_civic'],
                'created_by' => $staff1,
                'note' => 'Khách đã hẹn sẽ ghé showroom vào cuối tuần để xem thực tế xe Civic RS màu trắng ngọc trai.',
                'created_at' => $now->copy()->subDay(),
            ],

            // Lead Tuấn Anh Cross
            [
                'lead_id' => $leadIds['lead_anh_cross'],
                'created_by' => $staff1,
                'note' => 'Khách gọi điện hỏi chi tiết về tình trạng pin hybrid và chế độ bảo hành 7 năm của Toyota.',
                'created_at' => $now->copy()->subDays(4),
            ],
            [
                'lead_id' => $leadIds['lead_anh_cross'],
                'created_by' => $staff1,
                'note' => 'Đã xác nhận lịch lái thử Corolla Cross Hybrid sáng mai lúc 09:30. Đã yêu cầu rửa xe và kiểm tra áp suất lốp.',
                'created_at' => $now->copy()->subDays(1),
            ],

            // Lead Jane Buyer (Closed)
            [
                'lead_id' => $leadIds['lead_jane_ranger'],
                'created_by' => $staff1,
                'note' => 'Tiếp nhận thông tin khách quan tâm dòng bán tải Ranger phục vụ dã ngoại.',
                'created_at' => $now->copy()->subDays(13),
            ],
            [
                'lead_id' => $leadIds['lead_jane_ranger'],
                'created_by' => $staff1,
                'note' => 'Khách đã lái thử xe Ranger Wildtrak, đánh giá động cơ Bi-Turbo vận hành rất bốc và êm ái.',
                'created_at' => $now->copy()->subDays(8),
            ],
            [
                'lead_id' => $leadIds['lead_jane_ranger'],
                'created_by' => $staff1,
                'note' => 'Đã thương thảo chốt giá 910 triệu, tặng gói dán phim cách nhiệt 3M và nắp thùng cuộn điện.',
                'created_at' => $now->copy()->subDays(6),
            ],
            [
                'lead_id' => $leadIds['lead_jane_ranger'],
                'created_by' => $staff1,
                'note' => 'Khách đã hoàn tất thanh toán 100% và ký biên bản bàn giao xe.',
                'created_at' => $now->copy()->subDays(5),
            ],

            // Lead Mai City (Today appointment)
            [
                'lead_id' => $leadIds['lead_mai_city'],
                'created_by' => $staff3,
                'note' => 'Đã gọi điện tư vấn cho chị Mai về chiếc City RS CPO biển Hà Nội, odo 12,000 km.',
                'created_at' => $now->copy()->subDay(),
            ],
            [
                'lead_id' => $leadIds['lead_mai_city'],
                'created_by' => $staff3,
                'note' => 'Đã xác nhận lịch hẹn lái thử hôm nay lúc 10:30 sáng. Chuyên viên Linh chuẩn bị xe đón khách.',
                'created_at' => $now->copy()->subHours(3),
            ],

            // Lead Đạt Camry (Today appointment)
            [
                'lead_id' => $leadIds['lead_dat_camry'],
                'created_by' => $staff2,
                'note' => 'Khách để lại form đặt lịch xem xe Camry Hybrid 2025 chiều nay lúc 15:00.',
                'created_at' => $now->copy()->subHours(5),
            ],
            [
                'lead_id' => $leadIds['lead_dat_camry'],
                'created_by' => $staff2,
                'note' => 'Chuyên viên Hoàng đã gọi điện xác nhận và chuẩn bị sẵn xe mẫu ở sảnh trưng bày.',
                'created_at' => $now->copy()->subHours(2),
            ],

            // Lead Hương CX-5
            [
                'lead_id' => $leadIds['lead_huong_cx5'],
                'created_by' => $staff3,
                'note' => 'Đã gửi bảng tính so sánh phương án thanh toán thẳng và trả góp ngân hàng Shinhan Bank.',
                'created_at' => $now->copy()->subDays(3),
            ],
            [
                'lead_id' => $leadIds['lead_huong_cx5'],
                'created_by' => $staff3,
                'note' => 'Chị Hương đã lái thử chiếc CX-5 tuần trước, rất hài lòng về độ êm ái và gói an toàn i-Activsense.',
                'created_at' => $now->copy()->subDays(2),
            ],

            // Lead Quang Mazda 3
            [
                'lead_id' => $leadIds['lead_quang_mazda3'],
                'created_by' => $staff2,
                'note' => 'Đã gửi giấy chứng nhận kiểm định 176 hạng mục xe CPO cho anh Quang qua Zalo.',
                'created_at' => $now->copy()->subDays(3),
            ],
            [
                'lead_id' => $leadIds['lead_quang_mazda3'],
                'created_by' => $staff2,
                'note' => 'Đã xếp lịch hẹn lái thử vào cuối tuần. Anh Quang thông báo sẽ đưa người nhà có kinh nghiệm đi cùng kiểm tra xe.',
                'created_at' => $now->copy()->subDay(),
            ],

            // Lead Thảo Accent
            [
                'lead_id' => $leadIds['lead_thao_accent'],
                'created_by' => $staff3,
                'note' => 'Tư vấn phương án thu mua xe Grand i10 2018 cũ để đối trừ trực tiếp vào giá xe Accent 2024.',
                'created_at' => $now->copy()->subDay(),
            ],

            // Lead Dũng Everest
            [
                'lead_id' => $leadIds['lead_dung_everest'],
                'created_by' => $staff1,
                'note' => 'Khách hỏi chi tiết động cơ 2.0 Bi-Turbo và hệ dẫn động 2 cầu của chiếc Everest Titanium+ CPO.',
                'created_at' => $now->copy()->subDays(2),
            ],
            [
                'lead_id' => $leadIds['lead_dung_everest'],
                'created_by' => $staff1,
                'note' => 'Đã xác nhận lịch hẹn chiều mai lúc 14:00 để cả gia đình anh Dũng trải nghiệm không gian 7 chỗ.',
                'created_at' => $now->copy()->subHours(10),
            ],

            // Lead Yến Peugeot
            [
                'lead_id' => $leadIds['lead_yen_peugeot'],
                'created_by' => $staff3,
                'note' => 'Chị Yến đánh giá cao thiết kế i-Cockpit và cần số điện tử đặc trưng của Peugeot.',
                'created_at' => $now->copy()->subDays(3),
            ],
            [
                'lead_id' => $leadIds['lead_yen_peugeot'],
                'created_by' => $staff3,
                'note' => 'Lên lịch hẹn trải nghiệm hệ thống loa Focal và dàn tính năng tiện nghi vào ngày kia.',
                'created_at' => $now->copy()->subDay(),
            ],

            // Lead David Macan (Closed)
            [
                'lead_id' => $leadIds['lead_david_macan'],
                'created_by' => $staff2,
                'note' => 'Mr. David visited the showroom, inspected the Porsche Macan and reviewed the options list.',
                'created_at' => $now->copy()->subDays(15),
            ],
            [
                'lead_id' => $leadIds['lead_david_macan'],
                'created_by' => $staff2,
                'note' => 'Completed highway test drive, client was impressed by the Sport Chrono responsiveness.',
                'created_at' => $now->copy()->subDays(8),
            ],
            [
                'lead_id' => $leadIds['lead_david_macan'],
                'created_by' => $staff2,
                'note' => 'Contract signed, full payment received. Vehicle delivery scheduled with champagne handover.',
                'created_at' => $now->copy()->subDays(7),
            ],

            // Lead Tuấn (Lost)
            [
                'lead_id' => $leadIds['lead_tuan_lost'],
                'created_by' => $staff1,
                'note' => 'Khách gọi điện thông báo phải đi công tác nước ngoài 6 tháng nên tạm hoãn kế hoạch đổi xe.',
                'created_at' => $now->copy()->subDays(4),
            ],
            [
                'lead_id' => $leadIds['lead_tuan_lost'],
                'created_by' => $staff1,
                'note' => 'Chuyển trạng thái Lead sang Đã hủy (Lost). Lưu ghi chú chăm sóc lại vào quý sau.',
                'created_at' => $now->copy()->subDays(3),
            ],

            // Lead Nam (Lost)
            [
                'lead_id' => $leadIds['lead_nam_lost'],
                'created_by' => $staff2,
                'note' => 'Hồ sơ vay mua xe Santa Fe bị ngân hàng từ chối do lịch sử nợ quá hạn tín dụng.',
                'created_at' => $now->copy()->subDays(5),
            ],
            [
                'lead_id' => $leadIds['lead_nam_lost'],
                'created_by' => $staff2,
                'note' => 'Khách không có phương án đối ứng tiền mặt thay thế, chuyển lead sang trạng thái Đã hủy.',
                'created_at' => $now->copy()->subDays(4),
            ],
        ];

        foreach ($notes as &$note) {
            $note['updated_at'] = $note['created_at'];
        }
        unset($note);

        DB::table('lead_notes')->insert($notes);

        // =========================================================================
        // 3. SEED APPOINTMENTS (16 APPOINTMENTS - COVERING ALL STATUSES)
        // =========================================================================
        $appointments = [
            // --- 1. LỊCH HÔM NAY (TODAY) (2 lịch: 1 confirmed, 1 pending) ---
            [
                'user_id' => $users['mai.vu@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-CITY-001'] ?? null,
                'trim_id' => $trims['city-rs'] ?? null,
                'lead_id' => $leadIds['lead_mai_city'],
                'handled_by' => $staff3,
                'scheduled_at' => $today->copy()->setHour(10)->setMinute(30),
                'status' => 'confirmed',
                'note' => 'Khách nữ lái xe, chuẩn bị ghế lái vừa vặn và hướng dẫn kỹ các phím trên vô lăng.',
                'created_at' => $now->copy()->subHours(18),
                'updated_at' => $now->copy()->subHours(3),
            ],
            [
                'user_id' => $users['dat.do@gmail.com'] ?? null,
                'car_unit_id' => $units['NEW-CAMRY-001'] ?? null,
                'trim_id' => $trims['camry-25hev'] ?? null,
                'lead_id' => $leadIds['lead_dat_camry'],
                'handled_by' => $staff2,
                'scheduled_at' => $today->copy()->setHour(15)->setMinute(0),
                'status' => 'pending',
                'note' => 'Khách muốn lái thử khả năng tăng tốc của động cơ hybrid kết hợp mô-tơ điện.',
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(2),
            ],

            // --- 2. LỊCH CHỜ XÁC NHẬN (PENDING) (3 lịch) ---
            [
                'user_id' => $users['john@example.com'] ?? null,
                'car_unit_id' => $units['NEW-CIVIC-001'] ?? null,
                'trim_id' => $trims['civic-rs'] ?? null,
                'lead_id' => $leadIds['lead_john_civic'],
                'handled_by' => $staff1,
                'scheduled_at' => $now->copy()->addDays(2)->setHour(10)->setMinute(0),
                'status' => 'pending',
                'note' => 'Khách yêu cầu lái thử bản Civic RS trên đường cao tốc để test gói an toàn Honda Sensing.',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'user_id' => null,
                'car_unit_id' => $units['NEW-C300-001'] ?? null,
                'trim_id' => $trims['c300-amg'] ?? null,
                'lead_id' => $leadIds['lead_bao_c300'],
                'handled_by' => $staff2,
                'scheduled_at' => $now->copy()->addDays(4)->setHour(14)->setMinute(30),
                'status' => 'pending',
                'note' => 'Khách muốn xem nội thất ốp gỗ sồi xám và cửa sổ trời toàn cảnh.',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'user_id' => $users['tuan.nguyen@gmail.com'] ?? null,
                'car_unit_id' => $units['NEW-CAMRY-001'] ?? null,
                'trim_id' => $trims['camry-25hev'] ?? null,
                'lead_id' => $leadIds['lead_tuan_camry'],
                'handled_by' => $staff2,
                'scheduled_at' => $now->copy()->addDays(5)->setHour(9)->setMinute(0),
                'status' => 'pending',
                'note' => 'Khách muốn lái thử vào buổi sáng sớm, xem màu ngoại thất đen thực tế ngoài trời.',
                'created_at' => $now->copy()->subHours(8),
                'updated_at' => $now->copy()->subHours(8),
            ],

            // --- 3. LỊCH ĐÃ XÁC NHẬN (CONFIRMED) (5 lịch sắp tới) ---
            [
                'user_id' => null,
                'car_unit_id' => $units['USED-CROSS-001'] ?? null,
                'trim_id' => $trims['corolla-cross-hybrid'] ?? null,
                'lead_id' => $leadIds['lead_anh_cross'],
                'handled_by' => $staff1,
                'scheduled_at' => $now->copy()->addDay()->setHour(9)->setMinute(30),
                'status' => 'confirmed',
                'note' => 'Khách sẽ đến cùng vợ, chuẩn bị xe sạch sẽ và nạp đầy bình điện.',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDay(),
            ],
            [
                'user_id' => $users['dung.hoang@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-EVEREST-001'] ?? null,
                'trim_id' => $trims['everest-titanium-plus'] ?? null,
                'lead_id' => $leadIds['lead_dung_everest'],
                'handled_by' => $staff1,
                'scheduled_at' => $now->copy()->addDay()->setHour(14)->setMinute(0),
                'status' => 'confirmed',
                'note' => 'Khách đi cùng cả nhà 4 người, kiểm tra độ ngả lưng hàng ghế 3 và điều hòa sau.',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subHours(8),
            ],
            [
                'user_id' => $users['yen.bui@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-PEUGEOT-001'] ?? null,
                'trim_id' => $trims['peugeot-3008-gt'] ?? null,
                'lead_id' => $leadIds['lead_yen_peugeot'],
                'handled_by' => $staff3,
                'scheduled_at' => $now->copy()->addDays(2)->setHour(11)->setMinute(0),
                'status' => 'confirmed',
                'note' => 'Chuẩn bị danh sách bài hát chất lượng cao để thử dàn âm thanh Focal 10 loa.',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDay(),
            ],
            [
                'user_id' => $users['quang.tran@gmail.com'] ?? null,
                'car_unit_id' => $units['USED-MAZDA3-001'] ?? null,
                'trim_id' => $trims['mazda3-premium-sport'] ?? null,
                'lead_id' => $leadIds['lead_quang_mazda3'],
                'handled_by' => $staff2,
                'scheduled_at' => $now->copy()->addDays(3)->setHour(15)->setMinute(30),
                'status' => 'confirmed',
                'note' => 'Khách mang theo thợ để kiểm tra khung gầm và khoang máy xe lướt.',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDay(),
            ],
            [
                'user_id' => $users['khoa.nguyen@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-BMW320-001'] ?? null,
                'trim_id' => $trims['bmw-320i-m-sport'] ?? null,
                'lead_id' => $leadIds['lead_quang_mazda3'], // cùng gắn lead
                'handled_by' => $staff2,
                'scheduled_at' => $now->copy()->addDays(3)->setHour(16)->setMinute(30),
                'status' => 'confirmed',
                'note' => 'Lái thử tiếp chiếc BMW 320i để so sánh với Mazda 3.',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDay(),
            ],

            // --- 4. LỊCH ĐÃ HOÀN TẤT (DONE) (4 lịch trong quá khứ) ---
            [
                'user_id' => $users['jane@example.com'] ?? null,
                'car_unit_id' => $units['USED-RANGER-001'] ?? null,
                'trim_id' => $trims['ranger-wildtrak'] ?? null,
                'lead_id' => $leadIds['lead_jane_ranger'],
                'handled_by' => $staff1,
                'scheduled_at' => $now->copy()->subDays(7)->setHour(10)->setMinute(0),
                'status' => 'done',
                'note' => 'Lái thử đường dốc và kiểm tra tổng thể xe cũ trước khi ký hợp đồng mua.',
                'created_at' => $now->copy()->subDays(9),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'user_id' => $users['david.miller@example.com'] ?? null,
                'car_unit_id' => $units['NEW-MACAN-001'] ?? null,
                'trim_id' => $trims['porsche-macan-base'] ?? null,
                'lead_id' => $leadIds['lead_david_macan'],
                'handled_by' => $staff2,
                'scheduled_at' => $now->copy()->subDays(8)->setHour(14)->setMinute(0),
                'status' => 'done',
                'note' => 'Lái thử cao tốc và kiểm tra âm thanh ống xả thể thao.',
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(8),
            ],
            [
                'user_id' => $users['huong.le@gmail.com'] ?? null,
                'car_unit_id' => $units['CPO-CX5-001'] ?? null,
                'trim_id' => $trims['cx5-20-premium'] ?? null,
                'lead_id' => $leadIds['lead_huong_cx5'],
                'handled_by' => $staff3,
                'scheduled_at' => $now->copy()->subDays(4)->setHour(15)->setMinute(0),
                'status' => 'done',
                'note' => 'Lái thử đô thị, thử nghiệm tính năng phanh khoảng cách tự động.',
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'user_id' => null,
                'car_unit_id' => $units['NEW-VIOS-001'] ?? null,
                'trim_id' => $trims['vios-g'] ?? null,
                'lead_id' => $leadIds['lead_trang_vios_closed'],
                'handled_by' => $staff3,
                'scheduled_at' => $now->copy()->subDays(6)->setHour(9)->setMinute(30),
                'status' => 'done',
                'note' => 'Khách lái thử và đồng ý chốt hợp đồng mua ngay trong buổi sáng.',
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(6),
            ],

            // --- 5. LỊCH ĐÃ HỦY (CANCELLED) (2 lịch) ---
            [
                'user_id' => null,
                'car_unit_id' => $units['USED-CROSS-001'] ?? null,
                'trim_id' => $trims['corolla-cross-hybrid'] ?? null,
                'lead_id' => $leadIds['lead_tuan_lost'],
                'handled_by' => $staff1,
                'scheduled_at' => $now->copy()->subDays(3)->setHour(10)->setMinute(0),
                'status' => 'cancelled',
                'note' => 'Khách gọi điện xin hủy hẹn vì bận đi công tác nước ngoài đột xuất.',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'user_id' => null,
                'car_unit_id' => $units['NEW-SANTAFE-001'] ?? null,
                'trim_id' => $trims['santafe-calligraphy-turbo'] ?? null,
                'lead_id' => $leadIds['lead_nam_lost'],
                'handled_by' => $staff2,
                'scheduled_at' => $now->copy()->subDays(5)->setHour(16)->setMinute(0),
                'status' => 'cancelled',
                'note' => 'Hủy lịch hẹn sau khi không đạt tiêu chuẩn phê duyệt vay ngân hàng.',
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(5),
            ],
        ];

        DB::table('appointments')->insert($appointments);

        // =========================================================================
        // 4. SEED SALES (3 COMPLETED VEHICLE TRANSACTIONS)
        // =========================================================================
        $sales = [
            [
                'car_unit_id' => $units['USED-RANGER-001'] ?? 1,
                'buyer_user_id' => $users['jane@example.com'] ?? $users['john@example.com'],
                'created_by' => $staff1,
                'sold_price' => 910000000,
                'sold_at' => $now->copy()->subDays(5),
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'car_unit_id' => $units['NEW-MACAN-001'] ?? 2,
                'buyer_user_id' => $users['david.miller@example.com'] ?? $users['john@example.com'],
                'created_by' => $staff2,
                'sold_price' => 4850000000,
                'sold_at' => $now->copy()->subDays(7),
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'car_unit_id' => $units['NEW-VIOS-001'] ?? 3,
                'buyer_user_id' => $users['tuan.nguyen@gmail.com'] ?? $users['john@example.com'],
                'created_by' => $staff3,
                'sold_price' => 540000000,
                'sold_at' => $now->copy()->subDays(3),
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ],
        ];

        DB::table('sales')->insert($sales);

        // =========================================================================
        // 5. SEED TRIM REVIEWS (4 REVIEWS)
        // =========================================================================
        $reviews = [
            [
                'trim_id' => $trims['ranger-wildtrak'] ?? 1,
                'user_id' => $users['jane@example.com'] ?? null,
                'rating' => 5,
                'comment' => 'Động cơ Bi-Turbo vận hành rất khỏe và đầm chắc, tiện nghi không khác gì xe SUV cao cấp.',
                'status' => 'approved',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'trim_id' => $trims['porsche-macan-base'] ?? 2,
                'user_id' => $users['david.miller@example.com'] ?? null,
                'rating' => 5,
                'comment' => 'Exceptional driving dynamics, sharp steering and premium interior finish. Truly a Porsche.',
                'status' => 'approved',
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'trim_id' => $trims['corolla-cross-hybrid'] ?? 3,
                'user_id' => $users['john@example.com'] ?? null,
                'rating' => 5,
                'comment' => 'Xe hybrid chạy phố cực kỳ êm ái và tiết kiệm xăng. Mức tiêu hao chỉ tầm 4.2L/100km.',
                'status' => 'approved',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'trim_id' => $trims['camry-25hev'] ?? 4,
                'user_id' => $users['tuan.nguyen@gmail.com'] ?? null,
                'rating' => 5,
                'comment' => 'Thiết kế sang trọng, cách âm tuyệt vời. Động cơ xăng lai điện tăng tốc mượt mà không có độ trễ.',
                'status' => 'approved',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('trim_reviews')->insert($reviews);
    }
}
