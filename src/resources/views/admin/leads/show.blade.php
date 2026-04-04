@extends('admin.layouts.app')

@section('title', 'Chi tiet Lead')

@section('page-actions')
    <a href="{{ route('admin.appointments.create', ['lead_id' => $lead->id]) }}" class="admin-action-btn">Tao appointment</a>
    <a href="{{ route('admin.leads.index') }}" class="admin-action-btn admin-action-btn-secondary">Ve danh sach lead</a>
@endsection

@section('admin-content')
    @php($contextTrim = $lead->carUnit?->trim ?? $lead->trim)

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
                                <div><strong>Created:</strong> {{ optional($lead->created_at)->format('d/m/Y H:i') }}</div>
                                <div><strong>Status:</strong> <span class="admin-badge admin-badge-{{ $lead->status }}">{{ strtoupper($lead->status) }}</span></div>
                                <div><strong>Assigned:</strong> {{ $lead->assignedTo?->name ?? 'Chua phan cong' }}</div>
                                <div><strong>Context:</strong> {{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Lien he chung' }}</div>
                                @if ($lead->carUnit)
                                    <div><strong>Stock:</strong> {{ $lead->carUnit->stock_code }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body contacts_body">
                            <ul class="contacts">
                                @forelse ($lead->appointments->sortByDesc('scheduled_at') as $appointment)
                                    @php($appointmentTrim = $appointment->carUnit?->trim ?? $appointment->trim)
                                    <li class="{{ $loop->first ? 'active' : '' }}">
                                        <a href="{{ route('admin.appointments.edit', $appointment) }}">
                                            <div class="d-flex bd-highlight">
                                                <div class="img_cont">
                                                    <span class="admin-avatar-pill admin-avatar-pill-sm">{{ strtoupper(substr($appointment->status, 0, 1)) }}</span>
                                                </div>
                                                <div class="user_info">
                                                    <span>{{ optional($appointment->scheduled_at)->format('d/m/Y H:i') }}</span>
                                                    <p>{{ trim(collect([$appointmentTrim?->model?->make?->name, $appointmentTrim?->model?->name, $appointmentTrim?->name])->filter()->implode(' ')) ?: 'Khong co context xe' }}</p>
                                                </div>
                                                <span class="info">{{ strtoupper($appointment->status) }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="active">
                                        <a href="javascript:void(0)">
                                            <div class="d-flex bd-highlight">
                                                <div class="img_cont">
                                                    <span class="admin-avatar-pill admin-avatar-pill-sm">N</span>
                                                </div>
                                                <div class="user_info">
                                                    <span>Chua co appointment</span>
                                                    <p>Tao lich hen tu lead nay khi da qualify.</p>
                                                </div>
                                            </div>
                                        </a>
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
                                <div class="img_cont">
                                    <span class="admin-avatar-pill">{{ strtoupper(substr($lead->name, 0, 1)) }}</span>
                                </div>
                                <div class="user_info">
                                    <span>{{ $lead->name }}</span>
                                    <p>{{ $lead->source }} / {{ $lead->assignedTo?->name ?? 'Chua phan cong' }}</p>
                                </div>
                            </div>
                            <div class="btn-box">
                                <span class="admin-badge admin-badge-{{ $lead->status }}">{{ strtoupper($lead->status) }}</span>
                            </div>
                        </div>

                        <div class="card-body msg_card_body">
                            <form action="{{ route('admin.leads.update', $lead) }}" method="POST" class="row admin-message-form">
                                @csrf
                                @method('PATCH')

                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Ten lead</label>
                                        <input type="text" name="name" value="{{ old('name', $lead->name) }}" required>
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" required>
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ old('email', $lead->email) }}">
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Assigned to</label>
                                        <select name="assigned_to">
                                            <option value="">Chua phan cong</option>
                                            @foreach ($staffUsers as $staff)
                                                <option value="{{ $staff->id }}" @selected((string) old('assigned_to', $lead->assigned_to) === (string) $staff->id)>{{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-column col-lg-6">
                                    <div class="form_boxes">
                                        <label>Status</label>
                                        <select name="status" required>
                                            @foreach (['new', 'contacted', 'qualified', 'booked', 'closed', 'lost'] as $status)
                                                <option value="{{ $status }}" @selected(old('status', $lead->status) === $status)>{{ strtoupper($status) }}</option>
                                            @endforeach
                                        </select>
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
                                        <textarea name="message" rows="5">{{ old('message', $lead->message) }}</textarea>
                                    </div>
                                </div>
                                <div class="form-submit admin-form-submit-inline">
                                    <button type="submit" class="theme-btn btn-style-one">
                                        <span>Luu lead</span>
                                        <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow">
                                    </button>
                                </div>
                            </form>

                            <div class="admin-message-thread">
                                @forelse ($lead->notes->sortByDesc('created_at') as $note)
                                    <div class="d-flex justify-content-start mb-3">
                                        <div class="img_cont_msg">
                                            <span class="admin-avatar-pill admin-avatar-pill-sm">{{ strtoupper(substr($note->createdBy?->name ?? 'S', 0, 1)) }}</span>
                                            <div class="name">{{ $note->createdBy?->name ?? 'Staff' }} <span class="msg_time">{{ optional($note->created_at)->format('d/m/Y H:i') }}</span></div>
                                        </div>
                                        <div class="msg_cotainer admin-note-bubble">
                                            {{ $note->note }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="admin-empty-state">Chua co note nao cho lead nay.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card-footer">
                            <form action="{{ route('admin.leads.notes.store', $lead) }}" method="POST" class="form-group mb-0">
                                @csrf
                                <textarea class="form-control type_msg" name="note" placeholder="Them note follow-up, call result, next step..." required></textarea>
                                <button type="submit" class="theme-btn btn-style-one submit-btn">
                                    <span class="text-dk">Them note</span>
                                    <span class="text-mb">Luu</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <g clip-path="url(#lead-note-arrow)">
                                            <path d="M13.6109 0H5.05533C4.84037 0 4.66643 0.173943 4.66643 0.388901C4.66643 0.603859 4.84037 0.777802 5.05533 0.777802H12.6721L0.113697 13.3362C-0.0382246 13.4881 -0.0382246 13.7342 0.113697 13.8861C0.18964 13.962 0.289171 14 0.388666 14C0.488161 14 0.587656 13.962 0.663635 13.8861L13.222 1.3277V8.94447C13.222 9.15943 13.3959 9.33337 13.6109 9.33337C13.8259 9.33337 13.9998 9.15943 13.9998 8.94447V0.388901C13.9998 0.173943 13.8258 0 13.6109 0Z" fill="white"></path>
                                        </g>
                                        <defs>
                                            <clipPath id="lead-note-arrow">
                                                <rect width="14" height="14" fill="white"></rect>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
