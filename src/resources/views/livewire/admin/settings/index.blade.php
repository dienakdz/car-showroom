<div>
    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    <form wire:submit="save">
        <div class="gallery-sec admin-settings-shell">
            <div class="right-box-three admin-side-box">
                <h6 class="title">Showroom snapshot</h6>
                <div class="gallery-box">
                    <div class="inner-box admin-settings-gallery-box">
                        <div class="image-box admin-settings-card">
                            <div class="content-box">
                                <ul class="social-icon">
                                    <li><span>{{ substr(strtoupper((string) ($form['default_currency'] ?? 'VND')), 0, 3) }}</span></li>
                                    <li><span>{{ ($form['show_on_hold_public'] ?? false) ? 'ON' : 'OFF' }}</span></li>
                                </ul>
                            </div>
                            <div class="admin-settings-copy">
                                <span class="admin-overline">Brand</span>
                                <h4>{{ $form['brand_name'] ?: ($form['showroom_name'] ?: 'Car Showroom') }}</h4>
                                <p>{{ $form['showroom_phone'] ?: ($form['sales_hotline'] ?: '0900 000 000') }}</p>
                            </div>
                        </div>
                        <div class="uplode-box admin-settings-note">
                            <div class="content-box">
                                <span>Public policy</span>
                                <small>{{ ($form['email_lead_notifications'] ?? false) ? 'Email lead alerts dang bat.' : 'Email lead alerts dang tat.' }}</small>
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
                            <input type="text" wire:model.live.debounce.300ms="form.showroom_name" required>
                            @error('form.showroom_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form_boxes">
                            <label>Showroom phone</label>
                            <input type="text" wire:model.live.debounce.300ms="form.showroom_phone" required>
                            @error('form.showroom_phone') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form_boxes">
                            <label>Showroom email</label>
                            <input type="email" wire:model.blur="form.showroom_email">
                            @error('form.showroom_email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form_boxes">
                            <label>Showroom address</label>
                            <input type="text" wire:model.blur="form.showroom_address">
                            @error('form.showroom_address') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form_boxes">
                            <label>Description</label>
                            <textarea rows="5" wire:model.blur="form.showroom_description"></textarea>
                            @error('form.showroom_description') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
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
                                <input type="text" wire:model.live.debounce.300ms="form.brand_name" required>
                                @error('form.brand_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Default currency</label>
                                <input type="text" maxlength="3" wire:model.live.debounce.300ms="form.default_currency" required>
                                @error('form.default_currency') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Sales hotline</label>
                                <input type="text" wire:model.blur="form.sales_hotline">
                                @error('form.sales_hotline') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="admin-check-panel">
                                <input type="checkbox" wire:model.live="form.show_on_hold_public">
                                <span>
                                    <strong>Cho phep hien thi xe on_hold tren public</strong>
                                    <small>Neu tat, chi show xe `available` tren public site.</small>
                                </span>
                            </label>
                            @error('form.show_on_hold_public') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-lg-12">
                            <label class="admin-check-panel">
                                <input type="checkbox" wire:model.live="form.email_lead_notifications">
                                <span>
                                    <strong>Bat thong bao email cho lead</strong>
                                    <small>Placeholder cho workflow notification sau nay.</small>
                                </span>
                            </label>
                            @error('form.email_lead_notifications') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-lg-12">
                            <div class="form-submit admin-form-submit-end">
                                <button type="submit" class="theme-btn btn-style-one" wire:loading.attr="disabled" wire:target="save">
                                    <span wire:loading.remove wire:target="save">Luu settings</span>
                                    <span wire:loading wire:target="save">Dang luu...</span>
                                    <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow" wire:loading.remove wire:target="save">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
