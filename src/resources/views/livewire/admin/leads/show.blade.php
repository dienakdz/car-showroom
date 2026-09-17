@php
    $contextTrim = $lead->carUnit?->trim ?? $lead->trim;
    $contextName = trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' '));
    $contextMedia = $lead->carUnit?->primaryMedia ?? $contextTrim?->carUnits?->first()?->primaryMedia;
    $rawPath = $contextMedia?->path_or_url;
    $thumbUrl = null;
    if (filled($rawPath)) {
        $thumbUrl = (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://'))
            ? $rawPath
            : asset(ltrim($rawPath, '/'));
    }

    $pipelineStages = [
        'new' => ['order' => 1, 'title' => 'Mới tiếp nhận', 'desc' => 'Chưa liên hệ'],
        'contacted' => ['order' => 2, 'title' => 'Đang tư vấn / Lái thử', 'desc' => 'Tư vấn dòng xe'],
        'booked' => ['order' => 3, 'title' => 'Thương thảo hợp đồng', 'desc' => 'Chuẩn bị đặt cọc'],
        'closed' => ['order' => 4, 'title' => 'Chốt giao dịch', 'desc' => 'Giao dịch thành công'],
    ];

    $stageOrder = match ($lead->status) {
        'new' => 1,
        'contacted', 'qualified' => 2,
        'booked' => 3,
        'closed' => 4,
        default => 0,
    };
@endphp

<div class="c1-dash-wrapper">
    {{-- Alerts / Feedback --}}
    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 18px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa {{ ($feedback['type'] ?? 'success') === 'error' ? 'fa-exclamation-circle text-danger' : 'fa-check-circle text-success' }}" style="font-size: 16px;"></i>
                <span style="font-size: 13.5px; font-weight: 500;">{{ $feedback['message'] }}</span>
            </div>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="c1-page-header mb-4">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h1 class="c1-page-title mb-0">Lead #{{ $lead->id }} — {{ $lead->name }}</h1>
                @if ($lead->status === 'new')
                    <span class="c1-pill c1-pill-blue">Mới tiếp nhận</span>
                @elseif (in_array($lead->status, ['contacted', 'qualified'], true))
                    <span class="c1-pill c1-pill-indigo">Đang tư vấn</span>
                @elseif ($lead->status === 'booked')
                    <span class="c1-pill c1-pill-amber">Thương thảo / Lịch hẹn</span>
                @elseif ($lead->status === 'closed')
                    <span class="c1-pill c1-pill-green">Đã chốt xe</span>
                @else
                    <span class="c1-pill c1-pill-gray">Đã hủy</span>
                @endif
            </div>
            <div style="color: var(--c1-text-muted); font-size: 13px;">
                Nguồn: <strong>{{ $sourceOptions[$lead->source] ?? ucfirst($lead->source ?? 'Web') }}</strong> •
                Tiếp nhận lúc: <strong>{{ $lead->created_at?->format('d/m/Y H:i') }}</strong> ({{ $lead->created_at?->diffForHumans() }})
            </div>
        </div>
        <div class="c1-header-actions" style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('admin.appointments.create', ['lead_id' => $lead->id]) }}" class="c1-btn c1-btn-primary">
                <i class="fa fa-calendar-plus-o"></i>
                <span>Tạo lịch hẹn lái thử</span>
            </a>
            <a href="{{ route('admin.leads.index') }}" wire:navigate class="c1-action-btn">
                <i class="fa fa-arrow-left"></i>
                <span>Về danh sách</span>
            </a>
        </div>
    </div>

    {{-- Pipeline Stepper Bar --}}
    <div class="c1-pipeline-stepper mb-4">
        @foreach ($pipelineStages as $statusKey => $stage)
            @php
                $isCurrent = ($statusKey === 'contacted' && in_array($lead->status, ['contacted', 'qualified'], true)) || ($lead->status === $statusKey);
                $isPassed = $stageOrder > $stage['order'];
            @endphp
            <button
                type="button"
                wire:click="changeLeadStatus('{{ $statusKey }}')"
                wire:loading.attr="disabled"
                class="c1-stepper-step {{ $isCurrent ? 'active' : '' }} {{ $isPassed ? 'completed' : '' }}"
                title="Bấm để chuyển lead sang giai đoạn: {{ $stage['title'] }}"
            >
                <span class="c1-stepper-index">
                    @if ($isPassed)
                        <i class="fa fa-check"></i>
                    @else
                        {{ $stage['order'] }}
                    @endif
                </span>
                <div>
                    <div style="font-size: 13px; line-height: 1.2;">{{ $stage['title'] }}</div>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">{{ $stage['desc'] }}</div>
                </div>
            </button>
        @endforeach

        {{-- Nút Đánh dấu Hủy --}}
        <button
            type="button"
            wire:click="changeLeadStatus('lost')"
            wire:loading.attr="disabled"
            class="c1-stepper-step {{ $lead->status === 'lost' ? 'lost active' : '' }}"
            style="flex: 0 0 auto; min-width: 110px;"
            title="Bấm để đánh dấu lead này đã hủy / không thành công"
        >
            <span class="c1-stepper-index" style="background: {{ $lead->status === 'lost' ? '#ef4444' : '#fee2e2' }}; color: {{ $lead->status === 'lost' ? '#fff' : '#b91c1c' }};">
                <i class="fa fa-times"></i>
            </span>
            <div>
                <div style="font-size: 13px; line-height: 1.2;">Đã hủy</div>
                <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Không mua</div>
            </div>
        </button>
    </div>

    {{-- Main 2-Column CRM Dossier Layout --}}
    <div class="row">
        {{-- LEFT COLUMN: Lead Information & Vehicle Context --}}
        <div class="col-xl-7 col-lg-7 col-md-12 mb-4">
            {{-- Card 1: Thông tin khách hàng & Phân công --}}
            <div class="c1-panel mb-4" style="padding: 22px 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--c1-border-card);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb;">
                            <i class="fa fa-user"></i>
                        </span>
                        <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--c1-text-heading);">Thông tin khách hàng</h4>
                    </div>
                    <span class="c1-vehicle-tag">{{ $sourceLabels[$lead->source] ?? ucfirst($lead->source ?? 'Web') }}</span>
                </div>

                <form wire:submit="save">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 12.5px; font-weight: 600; color: var(--c1-text-body);">Họ và tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('form.name') is-invalid @enderror" wire:model="form.name" placeholder="Nguyễn Văn A">
                            @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 12.5px; font-weight: 600; color: var(--c1-text-body);">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('form.phone') is-invalid @enderror" wire:model="form.phone" placeholder="0901234567">
                            @error('form.phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 12.5px; font-weight: 600; color: var(--c1-text-body);">Địa chỉ Email</label>
                            <input type="email" class="form-control @error('form.email') is-invalid @enderror" wire:model="form.email" placeholder="khachhang@example.com">
                            @error('form.email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 12.5px; font-weight: 600; color: var(--c1-text-body);">Chuyên viên tư vấn phụ trách</label>
                            <select class="form-select @error('form.assigned_to') is-invalid @enderror" wire:model="form.assigned_to">
                                <option value="">-- Chưa phân công --</option>
                                @foreach ($staffUsers as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                @endforeach
                            </select>
                            @error('form.assigned_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 12.5px; font-weight: 600; color: var(--c1-text-body);">Trạng thái phễu (Status)</label>
                            <select class="form-select @error('form.status') is-invalid @enderror" wire:model="form.status">
                                @foreach ($statusOptions as $statusOption)
                                    <option value="{{ $statusOption }}">{{ strtoupper($statusOption) }}</option>
                                @endforeach
                            </select>
                            @error('form.status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 12.5px; font-weight: 600; color: var(--c1-text-muted);">Nguồn tiếp nhận (Source)</label>
                            <input type="text" class="form-control" value="{{ $sourceLabels[$lead->source] ?? $lead->source }}" disabled style="background: #f8fafc;">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" style="font-size: 12.5px; font-weight: 600; color: var(--c1-text-body);">Yêu cầu / Lời nhắn ban đầu của khách</label>
                            <textarea class="form-control @error('form.message') is-invalid @enderror" rows="3" wire:model="form.message" placeholder="Ví dụ: Khách quan tâm màu trắng, muốn lái thử vào thứ 7 tuần này..."></textarea>
                            @error('form.message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                        <button type="submit" class="c1-btn c1-btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save"><i class="fa fa-save me-1"></i> Lưu thông tin lead</span>
                            <span wire:loading wire:target="save"><i class="fa fa-spinner fa-spin me-1"></i> Đang lưu...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Card 2: Mẫu xe khách quan tâm trong kho --}}
            <div class="c1-panel mb-4" style="padding: 22px 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--c1-border-card);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #f0fdf4; color: #16a34a;">
                            <i class="fa fa-car"></i>
                        </span>
                        <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--c1-text-heading);">Mẫu xe khách đang quan tâm</h4>
                    </div>
                    @if ($lead->carUnit)
                        <span class="c1-pill c1-pill-green">Xe thực tế trong kho</span>
                    @elseif ($lead->trim)
                        <span class="c1-pill c1-pill-blue">Dòng xe catalog</span>
                    @endif
                </div>

                @if ($lead->carUnit || $lead->trim)
                    <div class="c1-vehicle-dossier-card">
                        <div class="c1-vehicle-dossier-thumb">
                            @if ($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="{{ $contextName }}">
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #94a3b8; font-size: 20px;">
                                    <i class="fa fa-car"></i>
                                </div>
                            @endif
                        </div>
                        <div class="c1-vehicle-dossier-info">
                            <h5 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: var(--c1-text-heading);">
                                {{ $contextName }}
                            </h5>
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 8px;">
                                @if ($lead->carUnit)
                                    <span style="font-size: 12px; color: var(--c1-text-muted);">Mã kho: <strong>#{{ $lead->carUnit->stock_code }}</strong></span>
                                    @if ($lead->carUnit->price)
                                        <span style="font-size: 13px; font-weight: 700; color: #16a34a;">{{ number_format((float) $lead->carUnit->price, 0, ',', '.') }} đ</span>
                                    @endif
                                @elseif ($lead->trim && $lead->trim->msrp)
                                    <span style="font-size: 13px; font-weight: 700; color: #16a34a;">Từ {{ number_format((float) $lead->trim->msrp, 0, ',', '.') }} đ</span>
                                @endif
                            </div>
                            <div>
                                @if ($lead->carUnit)
                                    <a href="{{ route('admin.inventory.edit', $lead->carUnit) }}" wire:navigate class="c1-action-btn c1-action-btn-primary" style="height: 28px; font-size: 12px;">
                                        <i class="fa fa-external-link"></i>
                                        <span>Xem chi tiết xe trong kho</span>
                                    </a>
                                @elseif ($lead->trim)
                                    <a href="{{ route('admin.catalog.trims.edit', $lead->trim) }}" wire:navigate class="c1-action-btn c1-action-btn-primary" style="height: 28px; font-size: 12px;">
                                        <i class="fa fa-external-link"></i>
                                        <span>Xem phiên bản catalog</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div style="padding: 20px; text-align: center; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; color: var(--c1-text-muted); font-size: 13px;">
                        <i class="fa fa-info-circle me-1"></i> Khách hàng để lại thông tin liên hệ chung, chưa chọn mẫu xe cụ thể.
                    </div>
                @endif
            </div>

            {{-- Card 3: Lịch hẹn lái thử liên kết --}}
            <div class="c1-panel" style="padding: 22px 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--c1-border-card);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #fef3c7; color: #d97706;">
                            <i class="fa fa-calendar-check-o"></i>
                        </span>
                        <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--c1-text-heading);">
                            Lịch hẹn lái thử & tư vấn ({{ $lead->appointments->count() }})
                        </h4>
                    </div>
                    <a href="{{ route('admin.appointments.create', ['lead_id' => $lead->id]) }}" class="c1-action-btn c1-action-btn-primary" style="height: 28px; font-size: 12px;">
                        <i class="fa fa-plus"></i>
                        <span>Thêm lịch hẹn</span>
                    </a>
                </div>

                @forelse ($lead->appointments->sortByDesc('scheduled_at') as $appointment)
                    @php($apptTrim = $appointment->carUnit?->trim ?? $appointment->trim)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                <strong style="font-size: 13.5px; color: var(--c1-text-heading);">
                                    <i class="fa fa-clock-o text-muted me-1"></i> {{ $appointment->scheduled_at?->format('d/m/Y H:i') }}
                                </strong>
                                @switch($appointment->status)
                                    @case('pending')
                                        <span class="c1-pill c1-pill-amber">Chờ xác nhận</span>
                                        @break
                                    @case('confirmed')
                                        <span class="c1-pill c1-pill-blue">Đã xác nhận</span>
                                        @break
                                    @case('done')
                                    @case('completed')
                                        <span class="c1-pill c1-pill-green">Đã hoàn tất</span>
                                        @break
                                    @case('cancelled')
                                        <span class="c1-pill c1-pill-gray">Đã hủy</span>
                                        @break
                                    @default
                                        <span class="c1-pill c1-pill-amber">{{ strtoupper($appointment->status) }}</span>
                                @endswitch

                            </div>
                            <div style="font-size: 12px; color: var(--c1-text-muted);">
                                Xe: 
                                @if ($appointment->carUnit)
                                    <strong>#{{ $appointment->carUnit->stock_code }}</strong> -
                                @endif
                                {{ trim(collect([$apptTrim?->model?->make?->name, $apptTrim?->model?->name, $apptTrim?->name])->filter()->implode(' ')) ?: 'Chưa chọn xe' }}
                                @if ($appointment->carUnit?->price)
                                    <span style="font-weight: 600; color: #16a34a; margin-left: 4px;">({{ number_format((float) $appointment->carUnit->price, 0, ',', '.') }} đ)</span>
                                @endif
                                • Phụ trách: <strong>{{ $appointment->handledBy?->name ?? 'Chưa chỉ định' }}</strong>
                            </div>
                        </div>
                        <a href="{{ route('admin.appointments.edit', $appointment) }}" class="c1-action-btn" title="Chỉnh sửa lịch hẹn">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
                @empty
                    <div style="padding: 20px; text-align: center; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; color: var(--c1-text-muted); font-size: 13px;">
                        Chưa có lịch hẹn nào cho lead này.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT COLUMN: Follow-up Activity & Notes Timeline --}}
        <div class="col-xl-5 col-lg-5 col-md-12">
            <div class="c1-panel" style="padding: 22px 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--c1-border-card);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #ede9fe; color: #7c3aed;">
                            <i class="fa fa-history"></i>
                        </span>
                        <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--c1-text-heading);">Nhật ký chăm sóc (Activity Log)</h4>
                    </div>
                    <span class="c1-pill c1-pill-indigo">{{ $lead->notes->count() }} ghi chú</span>
                </div>

                {{-- Form thêm ghi chú mới --}}
                <form wire:submit="addNote" class="mb-4">
                    <div class="mb-2">
                        <textarea
                            class="form-control @error('note') is-invalid @enderror"
                            rows="3"
                            wire:model="note"
                            placeholder="Nhập ghi chú cuộc gọi, kết quả tư vấn, nhu cầu phát sinh..."
                            required
                        ></textarea>
                        @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="c1-btn c1-btn-primary c1-btn-sm" wire:loading.attr="disabled" wire:target="addNote">
                            <span wire:loading.remove wire:target="addNote"><i class="fa fa-plus me-1"></i> Thêm ghi chú</span>
                            <span wire:loading wire:target="addNote"><i class="fa fa-spinner fa-spin me-1"></i> Đang lưu...</span>
                        </button>
                    </div>
                </form>

                {{-- Activity Timeline --}}
                <div class="c1-timeline mt-3">
                    @forelse ($lead->notes->sortByDesc('created_at') as $leadNote)
                        <div class="c1-timeline-item" wire:key="lead-note-{{ $leadNote->id }}">
                            <div class="c1-timeline-dot"></div>
                            <div class="c1-timeline-box">
                                <div class="c1-timeline-header">
                                    <div class="c1-timeline-author">
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: #2563eb; color: #fff; font-size: 10px; font-weight: 700;">
                                            {{ strtoupper(substr($leadNote->createdBy?->name ?? 'S', 0, 1)) }}
                                        </span>
                                        <span>{{ $leadNote->createdBy?->name ?? 'Chuyên viên' }}</span>
                                    </div>
                                    <span class="c1-timeline-time">{{ $leadNote->created_at?->diffForHumans() }} ({{ $leadNote->created_at?->format('d/m H:i') }})</span>
                                </div>
                                <div class="c1-timeline-body">{{ $leadNote->note }}</div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 24px 12px; text-align: center; color: var(--c1-text-muted); font-size: 13px;">
                            Chưa có ghi chú nào. Hãy ghi lại kết quả cuộc gọi hoặc trao đổi đầu tiên với khách hàng.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
