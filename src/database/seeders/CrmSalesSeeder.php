<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmSalesSeeder extends Seeder
{
    /**
     * Seed CRM, sales, and review data.
     */
    public function run(): void
    {
        $now = now();

        $userIds = DB::table('users')
            ->whereIn('email', ['staff@showroom.test', 'john@example.com', 'jane@example.com'])
            ->pluck('id', 'email');

        $trimIds = DB::table('trims')
            ->whereIn('slug', ['civic-rs', 'corolla-cross-hybrid', 'city-rs', 'ranger-wildtrak'])
            ->pluck('id', 'slug');

        $unitIds = DB::table('car_units')
            ->whereIn('stock_code', ['NEW-CIVIC-001', 'USED-CROSS-001', 'USED-RANGER-001', 'CPO-CITY-001'])
            ->pluck('id', 'stock_code');

        DB::table('appointments')->delete();
        DB::table('lead_notes')->delete();
        DB::table('leads')->delete();
        DB::table('sales')->delete();
        DB::table('trim_reviews')->delete();

        $leadOneId = DB::table('leads')->insertGetId([
            'user_id' => $userIds['john@example.com'],
            'car_unit_id' => $unitIds['NEW-CIVIC-001'],
            'trim_id' => $trimIds['civic-rs'],
            'assigned_to' => $userIds['staff@showroom.test'],
            'source' => 'unit_detail',
            'name' => 'John Buyer',
            'phone' => '0900000101',
            'email' => 'john@example.com',
            'message' => 'Toi can bao gia tra gop cho xe nay.',
            'status' => 'qualified',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'civic-rs-search',
            'created_at' => $now->copy()->subDays(4),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        $leadTwoId = DB::table('leads')->insertGetId([
            'user_id' => null,
            'car_unit_id' => $unitIds['USED-CROSS-001'],
            'trim_id' => $trimIds['corolla-cross-hybrid'],
            'assigned_to' => $userIds['staff@showroom.test'],
            'source' => 'trim_page',
            'name' => 'Nguyen Anh',
            'phone' => '0900000102',
            'email' => 'anh.nguyen@example.com',
            'message' => 'Toi muon dat lich test drive trong tuan nay.',
            'status' => 'booked',
            'utm_source' => 'facebook',
            'utm_medium' => 'social',
            'utm_campaign' => 'hybrid-awareness',
            'created_at' => $now->copy()->subDays(6),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        $leadThreeId = DB::table('leads')->insertGetId([
            'user_id' => $userIds['jane@example.com'],
            'car_unit_id' => $unitIds['USED-RANGER-001'],
            'trim_id' => $trimIds['ranger-wildtrak'],
            'assigned_to' => $userIds['staff@showroom.test'],
            'source' => 'contact',
            'name' => 'Jane Buyer',
            'phone' => '0900000103',
            'email' => 'jane@example.com',
            'message' => 'Toi quan tam mot mau pickup cho nhu cau di lai gia dinh.',
            'status' => 'closed',
            'utm_source' => null,
            'utm_medium' => null,
            'utm_campaign' => null,
            'created_at' => $now->copy()->subDays(12),
            'updated_at' => $now->copy()->subDays(5),
        ]);

        $leadFourId = DB::table('leads')->insertGetId([
            'user_id' => null,
            'car_unit_id' => $unitIds['CPO-CITY-001'],
            'trim_id' => $trimIds['city-rs'],
            'assigned_to' => $userIds['staff@showroom.test'],
            'source' => 'finance',
            'name' => 'Tran Minh',
            'phone' => '0900000104',
            'email' => 'minh.tran@example.com',
            'message' => 'Toi can uoc tinh phe duyet truoc cho ky han 60 thang.',
            'status' => 'new',
            'utm_source' => 'zalo',
            'utm_medium' => 'chat',
            'utm_campaign' => 'finance-form',
            'created_at' => $now->copy()->subDay(),
            'updated_at' => $now->copy()->subDay(),
        ]);

        DB::table('lead_notes')->insert([
            [
                'lead_id' => $leadOneId,
                'created_by' => $userIds['staff@showroom.test'],
                'note' => 'Da gui phuong an thanh toan va yeu cau chung minh thu nhap.',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'lead_id' => $leadTwoId,
                'created_by' => $userIds['staff@showroom.test'],
                'note' => 'Da xac nhan lich test drive sang thu Bay.',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'lead_id' => $leadThreeId,
                'created_by' => $userIds['staff@showroom.test'],
                'note' => 'Khach da hoan tat thanh toan va ky bien ban ban giao.',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
        ]);

        DB::table('appointments')->insert([
            [
                'user_id' => $userIds['john@example.com'],
                'car_unit_id' => $unitIds['NEW-CIVIC-001'],
                'trim_id' => $trimIds['civic-rs'],
                'lead_id' => $leadOneId,
                'handled_by' => $userIds['staff@showroom.test'],
                'scheduled_at' => $now->copy()->addDays(2),
                'status' => 'pending',
                'note' => 'Chuan bi bang mo phong khoan vay ban in.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => null,
                'car_unit_id' => $unitIds['USED-CROSS-001'],
                'trim_id' => $trimIds['corolla-cross-hybrid'],
                'lead_id' => $leadTwoId,
                'handled_by' => $userIds['staff@showroom.test'],
                'scheduled_at' => $now->copy()->addDay(),
                'status' => 'confirmed',
                'note' => 'Khach se den cung vo/chong.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $userIds['jane@example.com'],
                'car_unit_id' => $unitIds['USED-RANGER-001'],
                'trim_id' => $trimIds['ranger-wildtrak'],
                'lead_id' => $leadThreeId,
                'handled_by' => $userIds['staff@showroom.test'],
                'scheduled_at' => $now->copy()->subDays(7),
                'status' => 'done',
                'note' => 'Da hoan tat kiem tra cuoi truoc khi ban giao.',
                'created_at' => $now->copy()->subDays(8),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'user_id' => null,
                'car_unit_id' => $unitIds['CPO-CITY-001'],
                'trim_id' => $trimIds['city-rs'],
                'lead_id' => $leadFourId,
                'handled_by' => $userIds['staff@showroom.test'],
                'scheduled_at' => $now->copy()->addDays(4),
                'status' => 'pending',
                'note' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('sales')->insert([
            [
                'car_unit_id' => $unitIds['USED-RANGER-001'],
                'buyer_user_id' => $userIds['jane@example.com'],
                'created_by' => $userIds['staff@showroom.test'],
                'sold_price' => 910000000,
                'sold_at' => $now->copy()->subDays(5),
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
        ]);

        DB::table('trim_reviews')->insert([
            [
                'trim_id' => $trimIds['ranger-wildtrak'],
                'user_id' => $userIds['jane@example.com'],
                'rating' => 5,
                'comment' => 'Dong co manh va rat thuc dung cho cac chuyen di dai.',
                'status' => 'approved',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ],
        ]);
    }
}
