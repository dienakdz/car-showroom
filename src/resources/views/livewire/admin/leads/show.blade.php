@php($contextTrim = $lead->carUnit?->trim ?? $lead->trim)

<div>
    <div class="c1-page-header mb-4">
        <div>
            <h1 class="c1-page-title">Chi tiết lead #{{ $lead->id }}</h1>
            <p class="c1-page-subtitle">Cập nhật pipeline, ghi chú follow-up và nhân viên phụ trách.</p>
        </div>
        <div class="c1-header-actions">
            <a href="{{ route('admin.appointments.create', ['lead_id' => $lead->id]) }}" class="c1-btn c1-btn-primary">Tạo appointment</a>
            <a href="{{ route('admin.leads.index') }}" wire:navigate class="c1-action-btn">Về danh sách lead</a>
        </div>
    </div>

    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="chat-widget admin-chat-shell">
        <div class="widget-content">
            <div class="row">
                <div class="contacts_column col-xl-4 col-lg-5 col-md-12 col-sm-12 chat" id="chat_contacts">
                    <div class="card contacts_card admin-contacts-card">
                        <div class="card-header">
                            <div class="admin-contact-summary">
                                <div class="admin-avatar-pill">{{ strtoupper(substr($lead->name, 0, 1)) }}</div>
                                <div>
                                    <h5>{{ $lead->name }}</h5>
                                    <p>{{ $lead->phone }}{{ $lead->email ? ' / ' . $lead->email : '' }}</p>
                                </div>
                            </div>
                            <div class="admin-meta-list mt-3">
                                <div><strong>Source:</strong> {{ $lead->source }}</div>
                                <div><strong>Created:</strong> {{ $lead->created_at?->format('d/m/Y H:i') }}</div>
                                <div><strong>Status:</strong> <span class="admin-badge admin-badge-{{ $lead->status }}">{{ strtoupper($lead->status) }}</span></div>
                                <div><strong>Assigned:</strong> {{ $lead->assignedTo?->name ?? 'Chưa phân công' }}</div>
                                <div><strong>Context:</strong> {{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Liên hệ chung' }}</div>
                                @if ($lead->carUnit)
                                    <div><strong>Stock:</strong> {{ $lead->carUnit->stock_code }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body contacts_body">
                            <ul class="contacts">
                                @forelse ($lead->appointments->sortByDesc('scheduled_at') as $appointment)
                                    @php($appointmentTrim = $appointment->carUnit?->trim ?? $appointment->trim)
                                    <li class="{{ $loop->first ? 'active' : '' }}" wire:key="lead-appointment-{{ $appointment->id }}">
                                        <a href="{{ route('admin.appointments.edit', $appointment) }}">
                                            <div class="d-flex bd-highlight">
                                                <div class="img_cont">
                                                    <span class="admin-avatar-pill admin-avatar-pill-sm">{{ strtoupper(substr($appointment->status, 0, 1)) }}</span>
                                                </div>
                                                <div class="user_info">
                                                    <span>{{ $appointment->scheduled_at?->format('d/m/Y H:i') }}</span>
                                                    <p>{{ trim(collect([$appointmentTrim?->model?->make?->name, $appointmentTrim?->model?->name, $appointmentTrim?->name])->filter()->implode(' ')) ?: 'Không có context xe' }}</p>
                                                </div>
                                                <span class="info">{{ strtoupper($appointment->status) }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="active">
                                        <div class="d-flex bd-highlight">
                                            <div class="img_cont"><span class="admin-avatar-pill admin-avatar-pill-sm">N</span></div>
                                            <div class="user_info"><span>Chưa có appointment</span><p>Tạo lịch hẹn từ lead này khi đã qualify.</p></div>
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7 col-md-12 col-sm-12 chat">
                    <div class="card message-card admin-message-card">
                        <div class="card-header msg_head">
                            <div class="d-flex bd-highlight">
                                <div class="img_cont"><span class="admin-avatar-pill">{{ strtoupper(substr($lead->name, 0, 1)) }}</span></div>
                                <div class="user_info">
                                    <span>{{ $lead->name }}</span>
                                    <p>{{ $lead->source }} / {{ $lead->assignedTo?->name ?? 'Chưa phân công' }}</p>
                                </div>
                            </div>
                            <div class="btn-box"><span class="admin-badge admin-badge-{{ $lead->status }}">{{ strtoupper($lead->status) }}</span></div>
                        </div>

                        <div class="card-body msg_card_body">
                            <form wire:submit="save" class="row admin-message-form">
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Tên lead</label>
                                        <input type="text" wire:model.blur="form.name" required>
                                        @error('form.name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Phone</label>
                                        <input type="text" wire:model.blur="form.phone" required>
                                        @error('form.phone') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Email</label>
                                        <input type="email" wire:model.blur="form.email">
                                        @error('form.email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Assigned to</label>
                                        <select wire:model.blur="form.assigned_to">
                                            <option value="">Chưa phân công</option>
                                            @foreach ($staffUsers as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.assigned_to') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Status</label>
                                        <select wire:model.blur="form.status" required>
                                            @foreach ($statusOptions as $statusOption)
                                                <option value="{{ $statusOption }}">{{ strtoupper($statusOption) }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.status') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Source</label>
                                        <input type="text" value="{{ $lead->source }}" disabled>
                                    </div>
                                </div>
                                <div class="form-column col-lg-12">
                                    <div class="form_boxes">
                                        <label>Message</label>
                                        <textarea rows="5" wire:model.blur="form.message"></textarea>
                                        @error('form.message') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-submit admin-form-submit-inline">
                                    <button type="submit" class="theme-btn btn-style-one" wire:loading.attr="disabled" wire:target="save">
                                        <span wire:loading.remove wire:target="save">Lưu lead</span>
                                        <span wire:loading wire:target="save">Đang lưu...</span>
                                        <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow" wire:loading.remove wire:target="save">
                                    </button>
                                </div>
                            </form>

                            <div class="admin-message-thread">
                                @forelse ($lead->notes->sortByDesc('created_at') as $leadNote)
                                    <div class="d-flex justify-content-start mb-3" wire:key="lead-note-{{ $leadNote->id }}">
                                        <div class="img_cont_msg">
                                            <span class="admin-avatar-pill admin-avatar-pill-sm">{{ strtoupper(substr($leadNote->createdBy?->name ?? 'S', 0, 1)) }}</span>
                                            <div class="name">{{ $leadNote->createdBy?->name ?? 'Staff' }} <span class="msg_time">{{ $leadNote->created_at?->format('d/m/Y H:i') }}</span></div>
                                        </div>
                                        <div class="msg_cotainer admin-note-bubble">{{ $leadNote->note }}</div>
                                    </div>
                                @empty
                                    <div class="admin-empty-state">Chưa có note nào cho lead này.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card-footer">
                            <form wire:submit="addNote" class="form-group mb-0">
                                <textarea class="form-control type_msg" wire:model="note" placeholder="Thêm note follow-up, call result, next step..." required></textarea>
                                @error('note') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                <button type="submit" class="theme-btn btn-style-one submit-btn" wire:loading.attr="disabled" wire:target="addNote">
                                    <span class="text-dk" wire:loading.remove wire:target="addNote">Thêm note</span>
                                    <span class="text-mb" wire:loading.remove wire:target="addNote">Lưu</span>
                                    <span wire:loading wire:target="addNote">Đang lưu...</span>
                                    <svg wire:loading.remove wire:target="addNote" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M13.6109 0H5.05533C4.84037 0 4.66643 0.173943 4.66643 0.388901C4.66643 0.603859 4.84037 0.777802 5.05533 0.777802H12.6721L0.113697 13.3362C-0.0382246 13.4881 -0.0382246 13.7342 0.113697 13.8861C0.18964 13.962 0.289171 14 0.388666 14C0.488161 14 0.587656 13.962 0.663635 13.8861L13.222 1.3277V8.94447C13.222 9.15943 13.3959 9.33337 13.6109 9.33337C13.8259 9.33337 13.9998 9.15943 13.9998 8.94447V0.388901C13.9998 0.173943 13.8258 0 13.6109 0Z" fill="white"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
