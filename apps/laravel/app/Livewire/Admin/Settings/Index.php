<?php

namespace App\Livewire\Admin\Settings;

use App\Livewire\Admin\AdminPageComponent;
use App\Services\Admin\ShowroomSettingsService;
use App\Support\ViewDataCache;
use Illuminate\View\View;

class Index extends AdminPageComponent
{
    /** @var array<string, mixed> */
    public array $form = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    public function save(ShowroomSettingsService $service): void
    {
        $this->resetErrorBag();

        $validated = $this->validate(
            $this->rules(),
            attributes: $this->validationAttributes(),
        );

        $service->update($validated['form']);
        $this->form = $validated['form'];

        $this->toast('success', 'Đã cập nhật thông tin showroom và cấu hình hệ thống thành công.');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.index', [
            'currencyOptions' => [
                'VND' => 'VND (Việt Nam Đồng)',
                'USD' => 'USD (Đô la Mỹ)',
            ],
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Cài đặt hệ thống & Showroom',
            'adminPageDescription' => 'Quản lý thông tin showroom, nhận diện thương hiệu và chính sách vận hành.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'settings.manage';
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.showroom_name' => ['required', 'string', 'max:255'],
            'form.showroom_phone' => ['required', 'string', 'max:20'],
            'form.showroom_email' => ['nullable', 'email', 'max:255'],
            'form.showroom_address' => ['nullable', 'string', 'max:255'],
            'form.showroom_description' => ['nullable', 'string'],
            'form.brand_name' => ['required', 'string', 'max:255'],
            'form.default_currency' => ['required', 'string', 'in:VND,USD'],
            'form.sales_hotline' => ['nullable', 'string', 'max:20'],
            'form.show_on_hold_public' => ['boolean'],
            'form.email_lead_notifications' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.showroom_name' => 'Tên showroom',
            'form.showroom_phone' => 'Số điện thoại showroom',
            'form.showroom_email' => 'Email showroom',
            'form.showroom_address' => 'Địa chỉ showroom',
            'form.showroom_description' => 'Mô tả showroom',
            'form.brand_name' => 'Tên thương hiệu',
            'form.default_currency' => 'Đơn vị tiền tệ',
            'form.sales_hotline' => 'Hotline bán hàng',
            'form.show_on_hold_public' => 'Chính sách hiển thị xe giữ chỗ',
            'form.email_lead_notifications' => 'Thông báo email lead',
        ];
    }

    private function fillForm(): void
    {
        $showroom = ViewDataCache::rememberShowroom();
        $settings = ViewDataCache::rememberAdminSettings();

        $this->form = [
            'showroom_name' => (string) ($showroom->name ?? ''),
            'showroom_phone' => (string) ($showroom->phone ?? ''),
            'showroom_email' => (string) ($showroom->email ?? ''),
            'showroom_address' => (string) ($showroom->address ?? ''),
            'showroom_description' => (string) ($showroom->description ?? ''),
            'brand_name' => (string) data_get($settings, 'site.brand_name.value', $showroom->name ?? ''),
            'default_currency' => (string) data_get($settings, 'site.default_currency.value', 'VND'),
            'sales_hotline' => (string) data_get($settings, 'contact.sales_hotline.value', $showroom->phone ?? ''),
            'show_on_hold_public' => (bool) data_get($settings, 'inventory.show_on_hold_public.enabled', false),
            'email_lead_notifications' => (bool) data_get($settings, 'notifications.lead_email_enabled.enabled', false),
        ];
    }
}
