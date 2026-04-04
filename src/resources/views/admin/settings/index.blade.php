@extends('admin.layouts.app')

@section('title', 'Settings Admin')

@section('admin-content')
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="gallery-sec admin-settings-shell">
            <div class="right-box-three admin-side-box">
                <h6 class="title">Showroom snapshot</h6>
                <div class="gallery-box">
                    <div class="inner-box admin-settings-gallery-box">
                        <div class="image-box admin-settings-card">
                            <div class="content-box">
                                <ul class="social-icon">
                                    <li><a href="javascript:void(0)">{{ substr(strtoupper(data_get($adminSettings, 'site.default_currency.value', 'VND')), 0, 3) }}</a></li>
                                    <li><a href="javascript:void(0)">{{ old('show_on_hold_public', data_get($adminSettings, 'inventory.show_on_hold_public.enabled', false)) ? 'ON' : 'OFF' }}</a></li>
                                </ul>
                            </div>
                            <div class="admin-settings-copy">
                                <span class="admin-overline">Brand</span>
                                <h4>{{ old('brand_name', data_get($adminSettings, 'site.brand_name.value', $showroom?->name)) }}</h4>
                                <p>{{ $showroom?->phone ?? data_get($adminSettings, 'contact.sales_hotline.value', '0900 000 000') }}</p>
                            </div>
                        </div>
                        <div class="uplode-box admin-settings-note">
                            <div class="content-box">
                                <span>Public policy</span>
                                <small>{{ old('email_lead_notifications', data_get($adminSettings, 'notifications.lead_email_enabled.enabled', false)) ? 'Email lead alerts dang bat.' : 'Email lead alerts dang tat.' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="text">Khu nay dung de tom tat nhanh tinh trang van hanh cua showroom.</div>
                </div>
            </div>

            <div class="form-sec">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form_boxes">
                            <label>Showroom name</label>
                            <input type="text" name="showroom_name" value="{{ old('showroom_name', $showroom?->name ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form_boxes">
                            <label>Showroom phone</label>
                            <input type="text" name="showroom_phone" value="{{ old('showroom_phone', $showroom?->phone ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form_boxes">
                            <label>Showroom email</label>
                            <input type="email" name="showroom_email" value="{{ old('showroom_email', $showroom?->email ?? '') }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form_boxes">
                            <label>Showroom address</label>
                            <input type="text" name="showroom_address" value="{{ old('showroom_address', $showroom?->address ?? '') }}">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form_boxes">
                            <label>Description</label>
                            <textarea name="showroom_description" rows="5">{{ old('showroom_description', $showroom?->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="map-sec-two admin-settings-map-shell">
                <div class="form-sec-two">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Brand name</label>
                                <input type="text" name="brand_name" value="{{ old('brand_name', data_get($adminSettings, 'site.brand_name.value', $showroom?->name)) }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Default currency</label>
                                <input type="text" name="default_currency" maxlength="3" value="{{ old('default_currency', data_get($adminSettings, 'site.default_currency.value', 'VND')) }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Sales hotline</label>
                                <input type="text" name="sales_hotline" value="{{ old('sales_hotline', data_get($adminSettings, 'contact.sales_hotline.value', $showroom?->phone)) }}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="admin-check-panel">
                                <input type="hidden" name="show_on_hold_public" value="0">
                                <input type="checkbox" name="show_on_hold_public" value="1" {{ old('show_on_hold_public', data_get($adminSettings, 'inventory.show_on_hold_public.enabled', false)) ? 'checked' : '' }}>
                                <span>
                                    <strong>Cho phep hien thi xe on_hold tren public</strong>
                                    <small>Neu tat, chi show xe `available` tren public site.</small>
                                </span>
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <label class="admin-check-panel">
                                <input type="hidden" name="email_lead_notifications" value="0">
                                <input type="checkbox" name="email_lead_notifications" value="1" {{ old('email_lead_notifications', data_get($adminSettings, 'notifications.lead_email_enabled.enabled', false)) ? 'checked' : '' }}>
                                <span>
                                    <strong>Bat thong bao email cho lead</strong>
                                    <small>Placeholder cho workflow notification sau nay.</small>
                                </span>
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-submit admin-form-submit-end">
                                <button type="submit" class="theme-btn btn-style-one">
                                    <span>Luu settings</span>
                                    <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
