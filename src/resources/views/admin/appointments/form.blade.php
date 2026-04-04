@extends('admin.layouts.app')

@section('title', $appointment->exists ? 'Cap nhat Appointment' : 'Tao Appointment')

@section('page-actions')
    <a href="{{ route('admin.appointments.index') }}" class="admin-action-btn admin-action-btn-secondary">Ve danh sach</a>
@endsection

@section('admin-content')
    <form action="{{ $appointment->exists ? route('admin.appointments.update', $appointment) : route('admin.appointments.store') }}" method="POST">
        @csrf
        @if ($appointment->exists)
            @method('PATCH')
        @endif

        <div class="form-box admin-template-form-box admin-form-tabs-shell">
            <ul class="nav nav-tabs" id="appointment-form-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="appointment-booking-tab" data-bs-toggle="tab" data-bs-target="#appointment-booking" type="button" role="tab" aria-controls="appointment-booking" aria-selected="true">
                        Booking
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="appointment-context-tab" data-bs-toggle="tab" data-bs-target="#appointment-context" type="button" role="tab" aria-controls="appointment-context" aria-selected="false">
                        Context
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="appointment-form-tabs-content">
                <div class="tab-pane fade show active" id="appointment-booking" role="tabpanel" aria-labelledby="appointment-booking-tab">
                    <div class="row">
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Customer user</label>
                                <select name="user_id">
                                    <option value="">Khong linked user</option>
                                    @foreach ($customerUsers as $customer)
                                        <option value="{{ $customer->id }}" @selected((string) old('user_id', $appointment->user_id) === (string) $customer->id)>{{ $customer->name }}{{ $customer->phone ? ' / ' . $customer->phone : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Lead</label>
                                <select name="lead_id">
                                    <option value="">Khong linked lead</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->id }}" @selected((string) old('lead_id', $appointment->lead_id) === (string) $lead->id)>#{{ $lead->id }} - {{ $lead->name }} / {{ $lead->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Handled by</label>
                                <select name="handled_by">
                                    <option value="">Gan cho staff hien tai</option>
                                    @foreach ($staffUsers as $staff)
                                        <option value="{{ $staff->id }}" @selected((string) old('handled_by', $appointment->handled_by) === (string) $staff->id)>{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Status</label>
                                <select name="status" required>
                                    @foreach (['pending', 'confirmed', 'cancelled', 'done'] as $status)
                                        <option value="{{ $status }}" @selected(old('status', $appointment->status ?: 'pending') === $status)>{{ strtoupper($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Scheduled at</label>
                                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($appointment->scheduled_at)->format('Y-m-d\\TH:i')) }}" required>
                            </div>
                        </div>
                        <div class="form-column col-lg-12">
                            <div class="form_boxes">
                                <label>Note</label>
                                <textarea name="note" rows="5">{{ old('note', $appointment->note) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="appointment-context" role="tabpanel" aria-labelledby="appointment-context-tab">
                    <div class="row">
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Car unit</label>
                                <select name="car_unit_id">
                                    <option value="">Tu lead hoac chon sau</option>
                                    @foreach ($carUnits as $carUnit)
                                        <option value="{{ $carUnit->id }}" @selected((string) old('car_unit_id', $appointment->car_unit_id) === (string) $carUnit->id)>
                                            {{ $carUnit->stock_code }} / {{ $carUnit->trim?->model?->make?->name }} {{ $carUnit->trim?->model?->name }} {{ $carUnit->trim?->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Trim</label>
                                <select name="trim_id">
                                    <option value="">Tu car unit hoac lead</option>
                                    @foreach ($trims as $trim)
                                        <option value="{{ $trim->id }}" @selected((string) old('trim_id', $appointment->trim_id) === (string) $trim->id)>{{ $trim->model?->make?->name }} / {{ $trim->model?->name }} / {{ $trim->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-submit admin-form-submit-end">
                <button type="submit" class="theme-btn btn-style-one">
                    <span>{{ $appointment->exists ? 'Cap nhat appointment' : 'Tao appointment' }}</span>
                    <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow">
                </button>
            </div>
        </div>
    </form>

    @if ($appointment->exists)
        <div class="right-box-three admin-side-box mt-4">
            <h6 class="title">Appointment meta</h6>
            <div class="admin-meta-list">
                <div><strong>Created:</strong> {{ optional($appointment->created_at)->format('d/m/Y H:i') }}</div>
                <div><strong>Lead linked:</strong> {{ $appointment->lead?->name ?? 'Khong' }}</div>
                <div><strong>Handled by:</strong> {{ $appointment->handledBy?->name ?? 'Chua gan' }}</div>
            </div>
        </div>
    @endif
@endsection
