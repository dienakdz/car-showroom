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

        // Seed realistic customer notifications for customer accounts
        $customerEmails = ['tuan.nguyen@gmail.com', 'khoa.nguyen@gmail.com'];
        foreach ($customerEmails as $email) {
            $customer = User::where('email', $email)->first();
            if (! $customer instanceof User) {
                continue;
            }

            $customer->notifications()->delete();

            $customerNotifications = [
                [
                    'payload' => [
                        'category' => 'appointment',
                        'title' => 'Xác nhận lịch hẹn trải nghiệm xe thành công',
                        'message' => 'Lịch hẹn lái thử của bạn đã được showroom tiếp nhận. Chuyên viên dịch vụ sẽ đón tiếp và chuẩn bị xe chu đáo.',
                        'action_url' => route('account.show', ['tab' => 'appointments']),
                        'icon' => 'fa-solid fa-calendar-check',
                    ],
                    'created_at' => Carbon::now()->subHours(2),
                    'read_at' => null,
                ],
                [
                    'payload' => [
                        'category' => 'sale',
                        'title' => 'Bàn giao xe thành công & Kích hoạt bảo hành',
                        'message' => 'Chúc mừng bạn đã hoàn tất thủ tục nhận bàn giao xe. Hồ sơ bảo hành điện tử chính hãng đã được kích hoạt trong Gara của bạn.',
                        'action_url' => route('account.show', ['tab' => 'purchases']),
                        'icon' => 'fa-solid fa-car',
                    ],
                    'created_at' => Carbon::now()->subDays(2),
                    'read_at' => null,
                ],
                [
                    'payload' => [
                        'category' => 'review',
                        'title' => 'Đánh giá trải nghiệm của bạn đã được duyệt',
                        'message' => 'Cảm ơn bạn đã đóng góp đánh giá khách quan. Bài nhận xét xe của bạn đã được ban quản trị phê duyệt và đăng tải.',
                        'action_url' => route('account.show', ['tab' => 'reviews']),
                        'icon' => 'fa-solid fa-star',
                    ],
                    'created_at' => Carbon::now()->subDays(4),
                    'read_at' => Carbon::now()->subDays(3),
                ],
                [
                    'payload' => [
                        'category' => 'lead',
                        'title' => 'Báo giá dự toán lăn bánh và quà tặng khuyến mãi',
                        'message' => 'Chuyên viên tư vấn đã phản hồi yêu cầu của bạn cùng bảng tính gói tài chính trả góp ưu đãi 7.5%/năm.',
                        'action_url' => route('account.show', ['tab' => 'leads']),
                        'icon' => 'fa-solid fa-file-invoice-dollar',
                    ],
                    'created_at' => Carbon::now()->subDays(6),
                    'read_at' => Carbon::now()->subDays(5),
                ],
            ];

            foreach ($customerNotifications as $item) {
                $notification = new \App\Notifications\CustomerNotification($item['payload']);
                $customer->notify($notification);

                $customer->notifications()->latest('created_at')->first()?->update([
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['created_at'],
                    'read_at' => $item['read_at'],
                ]);
            }
        }
    }
}
