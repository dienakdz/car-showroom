<div>
    {{-- Header --}}
    <div class="c1-page-header mb-4" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 class="c1-page-title">Tạo hợp đồng bán xe</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                Chốt giao dịch offline, xuất hợp đồng mua bán và cập nhật trạng thái xe theo thời gian thực.
            </div>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="{{ route('admin.sales.index') }}" wire:navigate.hover class="c1-btn c1-btn-secondary">
                <i class="fa fa-arrow-left me-1"></i>
                <span>Về danh sách hợp đồng</span>
            </a>
            <button
                type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                class="c1-btn c1-btn-primary"
            >
                <span wire:loading.remove><i class="fa fa-check-circle me-1"></i> Chốt hợp đồng ngay</span>
                <span wire:loading><i class="fa fa-spinner fa-spin me-1"></i> Đang xử lý...</span>
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="c1-alert c1-alert-danger mb-4" style="padding: 14px 18px; border-radius: 8px;">
            <div style="font-weight: 600; margin-bottom: 6px;">
                <i class="fa fa-exclamation-triangle me-1"></i> Vui lòng kiểm tra lại thông tin:
            </div>
            <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row g-4">
            {{-- CỘT TRÁI (7 CỘT): KHAI BÁO THÔNG TIN DEAL & KHÁCH HÀNG --}}
            <div class="col-lg-7">
                {{-- Panel 1: Xe trong kho & Điều khoản giao dịch --}}
                <div class="c1-panel mb-4" style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--c1-border-card);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-car text-primary" style="font-size: 18px;"></i>
                            <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
                                1. Xe trong kho & Điều khoản giao dịch
                            </h4>
                        </div>
                        <span class="c1-pill c1-pill-blue" style="font-size: 11px;">Bắt buộc</span>
                    </div>

                    <div class="row g-3">
                        {{-- Chọn xe trong kho --}}
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 13px;">
                                Chọn chiếc xe trong kho cần chốt bán <span class="text-danger">*</span>
                            </label>
                            <select wire:model.live="form.car_unit_id" class="form-control" style="border-radius: 8px; height: 42px;">
                                <option value="">-- Chọn xe đang có sẵn trong kho ({{ $availableCarUnits->count() }} xe khả dụng) --</option>
                                @foreach ($availableCarUnits as $unit)
                                    <option value="{{ $unit->id }}">
                                        [{{ $unit->stock_code }}] {{ $unit->trim?->model?->make?->name }} {{ $unit->trim?->model?->name }} {{ $unit->trim?->name }} • {{ number_format((float) $unit->price, 0, ',', '.') }} VNĐ
                                    </option>
                                @endforeach
                            </select>
                            @error('form.car_unit_id') <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Live Vehicle Preview Card --}}
                        @if ($selectedCarUnit)
                            @php
                                $unit = $selectedCarUnit;
                                $coverMedia = $unit->primaryMedia;
                                $rawPath = $coverMedia?->path_or_url;
                                $coverUrl = filled($rawPath)
                                    ? ((str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) ? $rawPath : asset(ltrim($rawPath, '/')))
                                    : null;
                                $carTrim = $unit->trim;
                                $carTitle = implode(' ', array_filter([$carTrim?->model?->make?->name, $carTrim?->model?->name, $carTrim?->name]));
                            @endphp
                            <div class="col-12">
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; display: flex; gap: 16px; align-items: center;">
                                    <div style="width: 120px; height: 80px; border-radius: 8px; overflow: hidden; background: #0f172a; flex-shrink: 0; position: relative;">
                                        @if ($coverUrl)
                                            <img src="{{ $coverUrl }}" alt="{{ $carTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 18px;">
                                                <i class="fa fa-car"></i>
                                                <span style="font-size: 10px;">Chưa có ảnh</span>
                                            </div>
                                        @endif
                                        <span class="c1-pill c1-pill-green" style="position: absolute; top: 4px; right: 4px; font-size: 10px; padding: 2px 6px;">
                                            #{{ $unit->stock_code }}
                                        </span>
                                    </div>
                                    <div style="flex-grow: 1; min-width: 0;">
                                        <div style="font-weight: 700; font-size: 14px; color: var(--c1-text-heading); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $carTitle }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--c1-text-muted); margin-top: 4px; display: flex; flex-wrap: wrap; gap: 12px;">
                                            <span><i class="fa fa-paint-brush me-1"></i>{{ $unit->exteriorColor?->name ?? 'Màu ngoại thất chuẩn' }}</span>
                                            <span><i class="fa fa-cog me-1"></i>{{ $unit->transmission?->name ?? 'Tự động' }}</span>
                                            @if ($unit->year) <span><i class="fa fa-calendar me-1"></i>Đời {{ $unit->year }}</span> @endif
                                        </div>
                                        <div style="margin-top: 6px; font-size: 13px;">
                                            <span class="text-muted">Giá niêm yết kho:</span>
                                            <strong style="color: #16a34a; font-size: 14px; margin-left: 4px;">
                                                {{ number_format((float) $unit->price, 0, ',', '.') }} VNĐ
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Lead liên kết CRM --}}
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 13px;">
                                Liên kết hồ sơ Lead khách hàng CRM (nếu có)
                            </label>
                            <select wire:model.live="form.lead_id" class="form-control" style="border-radius: 8px; height: 42px;">
                                <option value="">-- Không liên kết Lead --</option>
                                @foreach ($leads as $lead)
                                    <option value="{{ $lead->id }}">
                                        #Lead-{{ $lead->id }} • {{ $lead->name }} ({{ $lead->phone }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted" style="font-size: 11.5px; margin-top: 4px;">
                                <i class="fa fa-info-circle me-1"></i> Khi chọn Lead, thông tin khách hàng và xe quan tâm sẽ được tự động đồng bộ.
                            </div>
                            @error('form.lead_id') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Giá chốt bán hợp đồng --}}
                        <div class="col-md-6">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label class="form-label mb-0" style="font-weight: 600; font-size: 13px;">
                                    Giá chốt hợp đồng <span class="text-danger">*</span>
                                </label>
                                @if ($selectedCarUnit)
                                    <button
                                        type="button"
                                        wire:click="applyListedPrice"
                                        class="btn btn-link p-0 text-primary"
                                        style="font-size: 11.5px; text-decoration: none;"
                                    >
                                        <i class="fa fa-magic me-1"></i> Dùng giá niêm yết
                                    </button>
                                @endif
                            </div>
                            <div class="input-group">
                                <input
                                    type="number"
                                    min="0"
                                    step="1000000"
                                    wire:model.live.debounce.300ms="form.sold_price"
                                    placeholder="VD: 850000000"
                                    class="form-control"
                                    style="border-radius: 8px 0 0 8px; height: 42px;"
                                >
                                <span class="input-group-text" style="border-radius: 0 8px 8px 0; font-size: 12px; font-weight: 600; background: #f8fafc;">VNĐ</span>
                            </div>

                            {{-- Live Price Comparison Calculation --}}
                            @if ($selectedCarUnit && filled($form['sold_price'] ?? null))
                                @php
                                    $listed = (int) $selectedCarUnit->price;
                                    $sold = (int) $form['sold_price'];
                                    $diff = $sold - $listed;
                                    $percent = $listed > 0 ? round(($diff / $listed) * 100, 1) : 0;
                                @endphp
                                <div style="margin-top: 6px; font-size: 12px; display: flex; align-items: center; gap: 8px;">
                                    @if ($diff < 0)
                                        <span class="c1-pill c1-pill-amber" style="font-size: 11px;">
                                            <i class="fa fa-arrow-down me-1"></i> Chiết khấu: -{{ number_format(abs($diff), 0, ',', '.') }} đ ({{ $percent }}%)
                                        </span>
                                    @elseif ($diff > 0)
                                        <span class="c1-pill" style="background: #f3e8ff; color: #7e22ce; font-size: 11px;">
                                            <i class="fa fa-arrow-up me-1"></i> Phụ thu / Chênh: +{{ number_format($diff, 0, ',', '.') }} đ (+{{ $percent }}%)
                                        </span>
                                    @else
                                        <span class="c1-pill c1-pill-green" style="font-size: 11px;">
                                            <i class="fa fa-check me-1"></i> Đúng giá niêm yết (100%)
                                        </span>
                                    @endif
                                </div>
                            @endif
                            @error('form.sold_price') <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Thời gian chốt giao dịch --}}
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 600; font-size: 13px; margin-bottom: 6px;">
                                Thời gian chốt hợp đồng <span class="text-danger">*</span>
                            </label>
                            <input
                                type="datetime-local"
                                wire:model="form.sold_at"
                                class="form-control"
                                style="border-radius: 8px; height: 42px;"
                            >
                            @error('form.sold_at') <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Panel 2: Thông tin khách hàng (Bên mua) --}}
                <div class="c1-panel mb-4" style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--c1-border-card);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-user-circle text-primary" style="font-size: 18px;"></i>
                            <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
                                2. Thông tin khách hàng (Bên mua xe)
                            </h4>
                        </div>

                        {{-- View Switcher Chế độ khách hàng --}}
                        <div class="c1-view-switcher" style="padding: 2px;">
                            <button
                                type="button"
                                wire:click="setBuyerMode('existing')"
                                class="c1-view-switcher-btn {{ $buyerMode === 'existing' ? 'active' : '' }}"
                                style="border: none; font-size: 12px; padding: 4px 10px; cursor: pointer;"
                            >
                                <i class="fa fa-users me-1"></i> Khách có sẵn
                            </button>
                            <button
                                type="button"
                                wire:click="setBuyerMode('new')"
                                class="c1-view-switcher-btn {{ $buyerMode === 'new' ? 'active' : '' }}"
                                style="border: none; font-size: 12px; padding: 4px 10px; cursor: pointer;"
                            >
                                <i class="fa fa-user-plus me-1"></i> + Khách hàng mới
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        @if ($buyerMode === 'existing')
                            <div class="col-12">
                                <label class="form-label" style="font-weight: 600; font-size: 13px;">
                                    Chọn khách hàng trong hệ thống <span class="text-danger">*</span>
                                </label>
                                <select wire:model.live="form.buyer_user_id" class="form-control" style="border-radius: 8px; height: 42px;">
                                    <option value="">-- Chọn khách hàng đã có tài khoản --</option>
                                    @foreach ($buyers as $buyer)
                                        <option value="{{ $buyer->id }}">
                                            {{ $buyer->name }} ({{ $buyer->phone ?: $buyer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.buyer_user_id') <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                            </div>

                            @if ($selectedBuyer)
                                @php
                                    $buyerUser = $selectedBuyer;
                                @endphp
                                <div class="col-12">
                                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <div style="font-weight: 700; color: #166534; font-size: 13.5px;">
                                                <i class="fa fa-check-circle me-1"></i> {{ $buyerUser->name }}
                                            </div>
                                            <div style="font-size: 12px; color: #15803d; margin-top: 2px;">
                                                SĐT: {{ $buyerUser->phone ?: 'Chưa cập nhật' }} • Email: {{ $buyerUser->email }}
                                            </div>
                                        </div>
                                        <span class="c1-pill c1-pill-green" style="font-size: 11px;">Hồ sơ hợp lệ</span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="col-12">
                                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; font-size: 12.5px; color: #1e40af;">
                                    <i class="fa fa-info-circle me-1"></i> Tài khoản khách hàng mới sẽ tự động được khởi tạo để theo dõi xe và lịch sử bảo hành.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: 600; font-size: 13px;">
                                    Họ và tên khách hàng <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="form.buyer_name"
                                    placeholder="Ví dụ: Nguyễn Văn An"
                                    class="form-control"
                                    style="border-radius: 8px; height: 42px;"
                                >
                                @error('form.buyer_name') <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: 600; font-size: 13px;">
                                    Số điện thoại liên hệ <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="form.buyer_phone"
                                    placeholder="Ví dụ: 0912345678"
                                    class="form-control"
                                    style="border-radius: 8px; height: 42px;"
                                >
                                @error('form.buyer_phone') <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" style="font-weight: 600; font-size: 13px;">
                                    Địa chỉ Email
                                </label>
                                <input
                                    type="email"
                                    wire:model="form.buyer_email"
                                    placeholder="Ví dụ: an.nguyen@example.com"
                                    class="form-control"
                                    style="border-radius: 8px; height: 42px;"
                                >
                                @error('form.buyer_email') <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI (5 CỘT): STICKY CONTRACT SUMMARY CARD --}}
            <div class="col-lg-5">
                <div style="position: sticky; top: 20px;">
                    {{-- Summary Card --}}
                    <div class="c1-panel mb-4" style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid var(--c1-border-card);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                            <h5 style="font-weight: 700; font-size: 15px; margin: 0; color: var(--c1-text-heading);">
                                <i class="fa fa-file-text-o text-primary me-2"></i> Phiếu tóm tắt hợp đồng
                            </h5>
                            <span class="c1-pill c1-pill-blue" style="font-size: 11px;">Xem trước</span>
                        </div>

                        {{-- Tóm tắt xe --}}
                        <div style="margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px dashed #e2e8f0;">
                            <div style="font-size: 12px; font-weight: 600; color: var(--c1-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                                Chiếc xe giao dịch
                            </div>
                            @if ($selectedCarUnit)
                                @php
                                    $unit = $selectedCarUnit;
                                    $trim = $unit->trim;
                                    $title = implode(' ', array_filter([$trim?->model?->make?->name, $trim?->model?->name, $trim?->name]));
                                @endphp
                                <div style="font-weight: 700; font-size: 14.5px; color: var(--c1-text-heading);">
                                    {{ $title }}
                                </div>
                                <div style="font-size: 12.5px; color: var(--c1-text-muted); margin-top: 2px;">
                                    Mã kho: <strong>#{{ $unit->stock_code }}</strong>
                                </div>
                            @else
                                <div style="font-size: 13px; color: #94a3b8; font-style: italic;">
                                    <i class="fa fa-exclamation-circle me-1"></i> Chưa chọn xe trong kho
                                </div>
                            @endif
                        </div>

                        {{-- Tóm tắt khách hàng --}}
                        <div style="margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px dashed #e2e8f0;">
                            <div style="font-size: 12px; font-weight: 600; color: var(--c1-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                                Khách hàng bên mua
                            </div>
                            @php
                                $displayBuyerName = $buyerMode === 'existing'
                                    ? ($selectedBuyer?->name)
                                    : ($form['buyer_name'] ?? '');
                                $displayBuyerPhone = $buyerMode === 'existing'
                                    ? ($selectedBuyer?->phone ?: $selectedBuyer?->email)
                                    : ($form['buyer_phone'] ?: ($form['buyer_email'] ?? ''));
                            @endphp
                            @if (filled($displayBuyerName))
                                <div style="font-weight: 700; font-size: 14px; color: var(--c1-text-heading);">
                                    {{ $displayBuyerName }}
                                </div>
                                @if (filled($displayBuyerPhone))
                                    <div style="font-size: 12.5px; color: var(--c1-text-muted); margin-top: 2px;">
                                        Liên hệ: {{ $displayBuyerPhone }}
                                    </div>
                                @endif
                            @else
                                <div style="font-size: 13px; color: #94a3b8; font-style: italic;">
                                    <i class="fa fa-exclamation-circle me-1"></i> Chưa có thông tin khách hàng
                                </div>
                            @endif
                        </div>

                        {{-- Thông tin thời gian & Nhân sự --}}
                        <div style="margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px dashed #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                                <span class="text-muted">Thời điểm ký kết:</span>
                                <strong>{{ filled($form['sold_at'] ?? null) ? \Carbon\Carbon::parse($form['sold_at'])->format('d/m/Y H:i') : '—' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-top: 6px;">
                                <span class="text-muted">Chuyên viên phụ trách:</span>
                                <span>{{ auth()->user()?->name ?? 'Admin' }}</span>
                            </div>
                        </div>

                        {{-- Tổng giá trị hợp đồng --}}
                        <div style="background: #f8fafc; border-radius: 8px; padding: 16px; margin-bottom: 20px; text-align: center; border: 1px solid #e2e8f0;">
                            <div style="font-size: 12px; color: var(--c1-text-muted); font-weight: 600; text-transform: uppercase;">
                                Tổng giá trị hợp đồng
                            </div>
                            <div style="font-size: 24px; font-weight: 800; color: #16a34a; margin-top: 4px;">
                                @if (filled($form['sold_price'] ?? null))
                                    {{ number_format((float) $form['sold_price'], 0, ',', '.') }} <span style="font-size: 14px; font-weight: 600;">VNĐ</span>
                                @elseif ($selectedCarUnit)
                                    {{ number_format((float) $selectedCarUnit->price, 0, ',', '.') }} <span style="font-size: 14px; font-weight: 600;">VNĐ (Niêm yết)</span>
                                @else
                                    0 <span style="font-size: 14px; font-weight: 600;">VNĐ</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <button
                                type="submit"
                                class="c1-btn c1-btn-primary w-100"
                                style="height: 46px; font-size: 14.5px; justify-content: center; font-weight: 700; box-shadow: 0 2px 6px rgba(37,99,235,0.25);"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove><i class="fa fa-check-circle me-1"></i> Xác nhận & Ký hợp đồng bán xe</span>
                                <span wire:loading><i class="fa fa-spinner fa-spin me-1"></i> Đang xử lý giao dịch...</span>
                            </button>

                            <a
                                href="{{ route('admin.sales.index') }}"
                                wire:navigate.hover
                                class="c1-btn c1-btn-secondary w-100"
                                style="height: 40px; justify-content: center;"
                            >
                                <i class="fa fa-times me-1"></i> Hủy & Quay lại
                            </a>
                        </div>
                    </div>

                    {{-- Workflow Info Box --}}
                    <div class="c1-panel p-3" style="background: #f8fafc; border-left: 4px solid var(--c1-primary); border-radius: 8px; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                        <div style="font-weight: 700; font-size: 12.5px; color: #1e293b; margin-bottom: 6px;">
                            <i class="fa fa-info-circle text-primary me-1"></i> Tự động hóa sau khi ký hợp đồng
                        </div>
                        <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #475569; line-height: 1.6;">
                            <li>Chiếc xe chuyển ngay sang trạng thái <strong>Đã bán (sold)</strong>.</li>
                            <li>Hồ sơ Lead CRM (nếu có) được đánh dấu <strong>Đã chốt (closed)</strong>.</li>
                            <li>Tự động ghi nhận doanh số và đối soát thanh toán showroom.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
