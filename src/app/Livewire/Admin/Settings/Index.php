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

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function save(ShowroomSettingsService $service): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->form = $this->normalizeForm($this->form);

        $validated = $this->validate(
            $this->rules(),
            attributes: $this->validationAttributes(),
        );

        $service->update($validated['form']);
        $this->form = $validated['form'];
        $this->feedback = [
            'type' => 'success',
            'message' => 'Da cap nhat showroom va settings.',
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.settings.index')
            ->layout('admin.layouts.livewire', $this->adminLayoutData([
                'adminPageTitle' => 'Settings & Showroom',
                'adminPageDescription' => 'Cap nhat thong tin showroom va mot so policy van hanh co ban.',
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
            'form.default_currency' => ['required', 'string', 'size:3'],
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
            'form.showroom_name' => 'ten showroom',
            'form.showroom_phone' => 'so dien thoai showroom',
            'form.showroom_email' => 'email showroom',
            'form.showroom_address' => 'dia chi showroom',
            'form.showroom_description' => 'mo ta showroom',
            'form.brand_name' => 'ten thuong hieu',
            'form.default_currency' => 'don vi tien te',
            'form.sales_hotline' => 'hotline ban hang',
            'form.show_on_hold_public' => 'chinh sach hien thi xe dang giu',
            'form.email_lead_notifications' => 'thong bao email lead',
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

    /**
     * @param  array<string, mixed>  $form
     * @return array<string, mixed>
     */
    private function normalizeForm(array $form): array
    {
        return [
            'showroom_name' => trim((string) ($form['showroom_name'] ?? '')),
            'showroom_phone' => trim((string) ($form['showroom_phone'] ?? '')),
            'showroom_email' => $this->nullableString($form['showroom_email'] ?? null),
            'showroom_address' => $this->nullableString($form['showroom_address'] ?? null),
            'showroom_description' => $this->nullableString($form['showroom_description'] ?? null),
            'brand_name' => trim((string) ($form['brand_name'] ?? '')),
            'default_currency' => strtoupper(trim((string) ($form['default_currency'] ?? 'VND'))),
            'sales_hotline' => $this->nullableString($form['sales_hotline'] ?? null),
            'show_on_hold_public' => (bool) ($form['show_on_hold_public'] ?? false),
            'email_lead_notifications' => (bool) ($form['email_lead_notifications'] ?? false),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }
}
