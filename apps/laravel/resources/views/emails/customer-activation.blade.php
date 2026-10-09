<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kích hoạt tài khoản thành viên</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.6;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f7f9fc;
            padding: 40px 15px;
            box-sizing: border-box;
        }
        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background-color: #050b20;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .email-header p {
            margin: 6px 0 0;
            color: #94a3b8;
            font-size: 13px;
        }
        .email-body {
            padding: 35px 30px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .text-content {
            font-size: 14px;
            color: #475569;
            margin-bottom: 24px;
            line-height: 1.7;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn-activate {
            display: inline-block;
            background-color: #405ff2;
            color: #ffffff !important;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(64, 95, 242, 0.25);
            letter-spacing: 0.3px;
        }
        .notice-box {
            background-color: #f8fafc;
            border-left: 4px solid #405ff2;
            padding: 14px 18px;
            border-radius: 4px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #64748b;
        }
        .fallback-link {
            font-size: 12px;
            color: #94a3b8;
            word-break: break-all;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed #e2e8f0;
        }
        .fallback-link a {
            color: #405ff2;
            text-decoration: underline;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
        }
        .email-footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <h1>{{ config('app.name', 'MD-CARS Showroom') }}</h1>
                <p>Hệ thống showroom ô tô cao cấp</p>
            </div>
            <div class="email-body">
                <div class="greeting">Xin chào {{ $user->name }},</div>
                <div class="text-content">
                    Cảm ơn quý khách đã tin tưởng và đăng ký tài khoản tại <strong>{{ config('app.name', 'MD-CARS Showroom') }}</strong>.<br>
                    Để hoàn tất việc kích hoạt tài khoản và bảo mật thông tin cá nhân, quý khách vui lòng xác nhận bằng cách nhấp vào nút bên dưới:
                </div>
                
                <div class="btn-wrapper">
                    <a href="{{ $activationUrl }}" class="btn-activate" target="_blank">KÍCH HOẠT TÀI KHOẢN NGAY</a>
                </div>

                <div class="notice-box">
                    <strong>Lưu ý:</strong> Liên kết kích hoạt này có hiệu lực trong vòng <strong>24 giờ</strong> kể từ thời điểm gửi. Sau thời gian này, quý khách có thể yêu cầu gửi lại liên kết mới từ trang đăng nhập.
                </div>

                <div class="text-content" style="font-size: 13px; color: #64748b; margin-bottom: 0;">
                    Nếu quý khách không thực hiện yêu cầu đăng ký này, xin vui lòng bỏ qua email. Tài khoản sẽ không hoạt động nếu chưa được kích hoạt.
                </div>

                <div class="fallback-link">
                    Nếu nút bấm trên không hoạt động, quý khách vui lòng sao chép và dán liên kết sau vào thanh địa chỉ của trình duyệt:<br>
                    <a href="{{ $activationUrl }}">{{ $activationUrl }}</a>
                </div>
            </div>
            <div class="email-footer">
                <p><strong>{{ config('app.name', 'MD-CARS Showroom') }}</strong></p>
                <p>Hotline CSKH: 1900 8888 | Email hỗ trợ: {{ config('mail.from.address', 'support@mdcars.vn') }}</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'MD-CARS Showroom') }}. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </div>
</body>
</html>
