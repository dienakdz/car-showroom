@extends('admin.layouts.app')

@section('title', 'Tao Sale')

@section('page-actions')
    <a href="{{ route('admin.sales.index') }}" class="admin-action-btn admin-action-btn-secondary">Ve sales log</a>
@endsection

@section('admin-content')
    <form action="{{ route('admin.sales.store') }}" method="POST">
        @csrf

        <div class="form-box admin-template-form-box admin-form-tabs-shell">
            <ul class="nav nav-tabs" id="sale-form-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="sale-deal-tab" data-bs-toggle="tab" data-bs-target="#sale-deal" type="button" role="tab" aria-controls="sale-deal" aria-selected="true">
                        Deal
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sale-buyer-tab" data-bs-toggle="tab" data-bs-target="#sale-buyer" type="button" role="tab" aria-controls="sale-buyer" aria-selected="false">
                        Buyer
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="sale-form-tabs-content">
                <div class="tab-pane fade show active" id="sale-deal" role="tabpanel" aria-labelledby="sale-deal-tab">
                    <div class="row">
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Car unit</label>
                                <select name="car_unit_id" required>
                                    <option value="">Chon xe can chot</option>
                                    @foreach ($availableCarUnits as $carUnit)
                                        <option value="{{ $carUnit->id }}" @selected((string) old('car_unit_id') === (string) $carUnit->id)>
                                            {{ $carUnit->stock_code }} / {{ $carUnit->trim?->model?->make?->name }} {{ $carUnit->trim?->model?->name }} {{ $carUnit->trim?->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Lead lien quan</label>
                                <select name="lead_id">
                                    <option value="">Khong linked lead</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->id }}" @selected((string) old('lead_id') === (string) $lead->id)>#{{ $lead->id }} - {{ $lead->name }} / {{ $lead->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Sold price</label>
                                <input type="number" min="0" name="sold_price" value="{{ old('sold_price') }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Sold at</label>
                                <input type="datetime-local" name="sold_at" value="{{ old('sold_at', now()->format('Y-m-d\\TH:i')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="sale-buyer" role="tabpanel" aria-labelledby="sale-buyer-tab">
                    <div class="row">
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Buyer co san</label>
                                <select name="buyer_user_id">
                                    <option value="">Tao / resolve buyer moi</option>
                                    @foreach ($buyers as $buyer)
                                        <option value="{{ $buyer->id }}" @selected((string) old('buyer_user_id') === (string) $buyer->id)>{{ $buyer->name }}{{ $buyer->phone ? ' / ' . $buyer->phone : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Buyer name</label>
                                <input type="text" name="buyer_name" value="{{ old('buyer_name') }}" placeholder="Dung khi chua co user">
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Buyer email</label>
                                <input type="email" name="buyer_email" value="{{ old('buyer_email') }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Buyer phone</label>
                                <input type="text" name="buyer_phone" value="{{ old('buyer_phone') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-submit admin-form-submit-end">
                <button type="submit" class="theme-btn btn-style-one">
                    <span>Chot sale</span>
                    <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow">
                </button>
            </div>
        </div>
    </form>

    <div class="right-box-three admin-side-box mt-4">
        <h6 class="title">Workflow note</h6>
        <div class="admin-meta-list">
            <div>Sale se doi `car_unit.status` sang `sold`.</div>
            <div>Neu co `lead_id`, lead se duoc chuyen sang `closed`.</div>
            <div>Buyer moi se duoc tao tu email/phone neu chua ton tai.</div>
        </div>
    </div>
@endsection
