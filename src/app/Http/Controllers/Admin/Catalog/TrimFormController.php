<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\UpsertTrimRequest;
use App\Models\CarAttribute;
use App\Models\CarModel;
use App\Models\FeatureGroup;
use App\Models\Trim;
use App\Services\Admin\TrimManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrimFormController extends AdminBaseController
{
    public function create(): View
    {
        return $this->formView(new Trim(), 'Tao trim moi', 'Khai bao phien ban va metadata chung de su dung cho inventory + review.');
    }

    public function store(UpsertTrimRequest $request, TrimManagementService $service): RedirectResponse
    {
        $trim = $service->save($request->validated());
        $this->pushSuccessToast('Da tao trim moi.');

        return redirect()->route('admin.catalog.trims.edit', $trim);
    }

    public function edit(Trim $trimRecord): View
    {
        return $this->formView(
            $trimRecord->load(['model.make', 'features', 'attributeValues.attribute']),
            'Cap nhat trim',
            'Dieu chinh spec, feature group va attribute value cho phien ban.'
        );
    }

    public function update(UpsertTrimRequest $request, Trim $trimRecord, TrimManagementService $service): RedirectResponse
    {
        $service->save($request->validated(), $trimRecord);
        $this->pushSuccessToast('Da cap nhat trim.');

        return redirect()->route('admin.catalog.trims.edit', $trimRecord);
    }

    protected function formView(Trim $trim, string $title, string $description): View
    {
        return $this->adminView('admin.catalog.trims.form', [
            'adminPageTitle' => $title,
            'adminPageDescription' => $description,
            'catalogTab' => 'trims',
            'trimRecord' => $trim,
            'models' => CarModel::query()->with('make')->orderBy('name')->get(),
            'featureGroups' => FeatureGroup::query()
                ->with('features')
                ->orderBy('sort_order')
                ->get(),
            'attributes' => CarAttribute::query()->orderBy('sort_order')->get(),
        ]);
    }
}
