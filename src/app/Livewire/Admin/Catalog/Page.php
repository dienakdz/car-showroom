<?php

namespace App\Livewire\Admin\Catalog;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Trim;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\View\View;

class Page extends AdminPageComponent
{
    private const TABS = ['makes', 'models', 'trims'];

    #[Url(history: true, keep: true)]
    public string $tab = 'makes';

    public function mount(): void
    {
        $this->ensureValidTab();
    }

    #[On('catalog-updated')]
    public function refreshCatalog(): void
    {
        // Intentionally empty: the event forces a rerender so summary cards stay in sync.
    }

    public function switchTab(string $tab): void
    {
        $this->tab = $tab;
        $this->ensureValidTab();
    }

    public function render(): View
    {
        return view('livewire.admin.catalog.page', [
            'summary' => [
                'makes' => Make::query()->count(),
                'models' => CarModel::query()->count(),
                'trims' => Trim::query()->count(),
            ],
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Catalog Workspace',
            'adminPageDescription' => 'Quan ly makes, models va trims theo module ro rang, uu tien thao tac nhanh va khong reload.',
        ]));
    }

    private function ensureValidTab(): void
    {
        if (! in_array($this->tab, self::TABS, true)) {
            $this->tab = self::TABS[0];
        }
    }
}
