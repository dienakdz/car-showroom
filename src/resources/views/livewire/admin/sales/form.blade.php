<div>
    <div class="c1-page-header mb-4" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="c1-page-title">Tạo hợp đồng bán xe</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                Chốt giao dịch offline, kết nối khách hàng và lưu trữ hợp đồng mua bán xe.
            </div>
        </div>
        <div>
            <a href="{{ route('admin.sales.index') }}" wire:navigate class="admin-action-btn admin-action-btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Về danh sách hợp đồng
            </a>
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
        <div class="form-box admin-template-form-box admin-form-tabs-shell" style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            {{-- Tabs Header --}}
            <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 24px; border-bottom: 1px solid #e2e8f0;">
                <li class="nav-item" role="presentation">
                    <button
                        type="button"
                        class="nav-link {{ $activeTab === 'deal' ? 'active font-weight-bold' : '' }}"
                        wire:click="switchTab('deal')"
                        style="cursor: pointer;"
                    >
                        <i class="fa fa-car me-1"></i> 1. Thông tin giao dịch & Xe (Deal)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button
                        type="button"
                        class="nav-link {{ $activeTab === 'buyer' ? 'active font-weight-bold' : '' }}"
                        wire:click="switchTab('buyer')"
                        style="cursor: pointer;"
                    >
                        <i class="fa fa-user me-1"></i> 2. Thông tin khách hàng (Buyer)
                    </button>
                </li>
            </ul>

            {{-- Tabs Content --}}
            <div class="tab-content">
                {{-- TAB 1: DEAL --}}
                <div class="tab-pane {{ $activeTab === 'deal' ? 'active show' : '' }}">
                    <div class="row">
                        <div class="form-column col-lg-6 mb-3">
                            <div class="form_boxes">
                                <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block;">
                                    Xe trong kho cần chốt <span class="text-danger">*</span>
                                </label>
                                <select wire:model.live="form.car_unit_id" class="form-control" style="border-radius: 8px; height: 44px;">
                                    <option value="">-- Chọn xe trong kho --</option>
                                    @foreach ($availableCarUnits as $carUnit)
                                        <option value="{{ $carUnit->id }}">
                                            [{{ $carUnit->stock_code }}] {{ $carUnit->trim?->model?->make?->name }} {{ $carUnit->trim?->model?->name }} {{ $carUnit->trim?->name }} - {{ number_format($carUnit->price, 0, ',', '.') }} đ
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.car_unit_id')
                                    <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-6 mb-3">
                            <div class="form_boxes">
                                <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block;">
                                    Lead khách hàng liên kết (nếu có)
                                </label>
                                <select wire:model.live="form.lead_id" class="form-control" style="border-radius: 8px; height: 44px;">
                                    <option value="">-- Không liên kết lead --</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->id }}">
                                            #{{ $lead->id }} - {{ $lead->name }} ({{ $lead->phone }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.lead_id')
                                    <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-6 mb-3">
                            <div class="form_boxes">
                                <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block;">
                                    Giá chốt hợp đồng (VNĐ) <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    min="0"
                                    step="1000000"
                                    wire:model="form.sold_price"
                                    placeholder="VD: 850000000"
                                    class="form-control"
                                    style="border-radius: 8px; height: 44px;"
                                >
                                @if (!empty($form['sold_price']))
                                    <div class="text-muted" style="font-size: 12px; margin-top: 4px;">
                                        Tương đương: <strong>{{ number_format((float) $form['sold_price'], 0, ',', '.') }} VNĐ</strong>
                                    </div>
                                @endif
                                @error('form.sold_price')
                                    <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-6 mb-3">
                            <div class="form_boxes">
                                <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block;">
                                    Thời gian chốt hợp đồng <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    wire:model="form.sold_at"
                                    class="form-control"
                                    style="border-radius: 8px; height: 44px;"
                                >
                                @error('form.sold_at')
                                    <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 16px; text-align: right;">
                        <button
                            type="button"
                            class="c1-btn c1-btn-primary"
                            wire:click="switchTab('buyer')"
                        >
                            Tiếp tục: Thông tin khách hàng <i class="fa fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- TAB 2: BUYER --}}
                <div class="tab-pane {{ $activeTab === 'buyer' ? 'active show' : '' }}">
                    <div class="row">
                        <div class="form-column col-lg-12 mb-3">
                            <div class="form_boxes">
                                <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block;">
                                    Khách hàng có sẵn trong hệ thống
                                </label>
                                <select wire:model.live="form.buyer_user_id" class="form-control" style="border-radius: 8px; height: 44px;">
                                    <option value="">-- Nhập thông tin khách hàng mới bên dưới --</option>
                                    @foreach ($buyers as $buyer)
                                        <option value="{{ $buyer->id }}">
                                            {{ $buyer->name }} ({{ $buyer->phone ?: $buyer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.buyer_user_id')
                                    <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-4 mb-3">
                            <div class="form_boxes">
                                <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block;">
                                    Họ và tên khách hàng @if(empty($form['buyer_user_id'])) <span class="text-danger">*</span> @endif
                                </label>
                                <input
                                    type="text"
                                    wire:model="form.buyer_name"
                                    placeholder="VD: Nguyễn Văn An"
                                    class="form-control"
                                    style="border-radius: 8px; height: 44px;"
                                >
                                @error('form.buyer_name')
                                    <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-4 mb-3">
                            <div class="form_boxes">
                                <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block;">
                                    Số điện thoại liên hệ @if(empty($form['buyer_user_id'])) <span class="text-danger">*</span> @endif
                                </label>
                                <input
                                    type="text"
                                    wire:model="form.buyer_phone"
                                    placeholder="VD: 0912345678"
                                    class="form-control"
                                    style="border-radius: 8px; height: 44px;"
                                >
                                @error('form.buyer_phone')
                                    <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-4 mb-3">
                            <div class="form_boxes">
                                <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block;">
                                    Địa chỉ Email
                                </label>
                                <input
                                    type="email"
                                    wire:model="form.buyer_email"
                                    placeholder="VD: an.nguyen@example.com"
                                    class="form-control"
                                    style="border-radius: 8px; height: 44px;"
                                >
                                @error('form.buyer_email')
                                    <span class="text-danger" style="font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                        <button
                            type="button"
                            class="c1-btn c1-btn-secondary"
                            wire:click="switchTab('deal')"
                        >
                            <i class="fa fa-arrow-left me-1"></i> Quay lại Deal
                        </button>

                        <button
                            type="submit"
                            class="theme-btn btn-style-one"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove><i class="fa fa-check-circle me-1"></i> Xác nhận & Chốt hợp đồng</span>
                            <span wire:loading><i class="fa fa-spinner fa-spin me-1"></i> Đang xử lý...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Workflow Explanation Box --}}
    <div class="right-box-three admin-side-box mt-4" style="background: #f8fafc; border-left: 4px solid var(--c1-primary); padding: 16px 20px; border-radius: 8px;">
        <h6 class="title" style="margin-bottom: 8px; font-weight: 700; color: #1e293b;">
            <i class="fa fa-info-circle me-1" style="color: var(--c1-primary);"></i> Quy trình tự động khi hoàn tất tạo hợp đồng
        </h6>
        <div class="admin-meta-list" style="font-size: 13px; color: #475569; line-height: 1.6;">
            <div>• Trạng thái chiếc xe trong kho (<code>car_unit</code>) sẽ được tự động chuyển sang <strong>Đã bán (sold)</strong>.</div>
            <div>• Nếu có liên kết Lead, khách hàng tiềm năng đó sẽ được cập nhật trạng thái <strong>Đã đóng (closed)</strong>.</div>
            <div>• Tài khoản khách hàng (buyer) mới sẽ tự động được tạo và liên kết thông tin nếu chưa tồn tại trong hệ thống.</div>
        </div>
    </div>
</div>
