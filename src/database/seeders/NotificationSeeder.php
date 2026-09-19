<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\AdminSystemNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@showroom.test')->first();

        if (! $admin instanceof User) {
            return;
        }

        // Clear existing notifications
        $admin->notifications()->delete();

        $notifications = [
            [
                'payload' => [
                    'category' => 'appointment',
                    'title' => 'Lịch hẹn lái thử xe mới',
                    'message' => 'Khách hàng Nguyễn Anh Tuấn đã đặt lịch lái thử mẫu xe Toyota Camry 2.5HEV vào 09:00 AM ngày 23/09/2026.',
                    'action_url' => route('admin.appointments.index'),
                    'icon' => 'fa fa-calendar-check',
                    'meta' => ['lead_id' => 3, 'customer_name' => 'Nguyễn Anh Tuấn'],
                ],
                'created_at' => Carbon::now()->subMinutes(2),
                'read_at' => null,
            ],
            [
                'payload' => [
                    'category' => 'sale',
                    'title' => 'Hợp đồng mua bán đã hoàn tất',
                    'message' => 'Hợp đồng #HD-001 chiếc Mercedes-Benz C-Class C 300 AMG của khách hàng Đỗ Thành Đạt đã thanh toán đủ 1.980.000.000 VND.',
                    'action_url' => route('admin.sales.index'),
                    'icon' => 'fa fa-handshake',
                    'meta' => ['sale_code' => 'HD-001', 'amount' => 1980000000],
                ],
                'created_at' => Carbon::now()->subMinutes(15),
                'read_at' => null,
            ],
            [
                'payload' => [
                    'category' => 'lead',
                    'title' => 'Khách hàng mới để lại thông tin cần tư vấn',
                    'message' => 'Khách hàng Lâm Gia Bảo vừa gửi form liên hệ quan tâm xe Mercedes-Benz C-Class C 300 AMG.',
                    'action_url' => route('admin.leads.index'),
                    'icon' => 'fa fa-user-plus',
                    'meta' => ['lead_name' => 'Lâm Gia Bảo', 'phone' => '0933112233'],
                ],
                'created_at' => Carbon::now()->subHours(1),
                'read_at' => null,
            ],
            [
                'payload' => [
                    'category' => 'review',
                    'title' => 'Đánh giá xe mới cần duyệt',
                    'message' => 'Khách hàng Nguyễn Anh Tuấn vừa gửi đánh giá 5 sao cho phiên bản Toyota Camry 2.5HEV.',
                    'action_url' => route('admin.reviews.index'),
                    'icon' => 'fa fa-star',
                    'meta' => ['rating' => 5, 'trim' => 'Toyota Camry 2.5HEV'],
                ],
                'created_at' => Carbon::now()->subHours(3),
                'read_at' => Carbon::now()->subHours(2),
            ],
            [
                'payload' => [
                    'category' => 'inventory',
                    'title' => 'Cảnh báo giữ cọc xe',
                    'message' => 'Chiếc Porsche Macan CPO (Mã kho CPO-MACAN-001) đang giữ chỗ đã quá 48 giờ chưa ký hợp đồng.',
                    'action_url' => route('admin.inventory.index'),
                    'icon' => 'fa fa-clock',
                    'meta' => ['stock_code' => 'CPO-MACAN-001'],
                ],
                'created_at' => Carbon::now()->subDay(),
                'read_at' => Carbon::now()->subHours(12),
            ],
        ];

        foreach ($notifications as $item) {
            $notification = new AdminSystemNotification($item['payload']);
            $admin->notify($notification);

            // Update created_at and read_at to simulate realistic history
            $admin->notifications()->latest('created_at')->first()?->update([
                'created_at' => $item['created_at'],
                'updated_at' => $item['created_at'],
                'read_at' => $item['read_at'],
            ]);
        }
    }
}
