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
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrimController extends AdminBaseController
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $modelId = $request->integer('model_id');

        $trims = Trim::query()
            ->with('model.make')
            ->withCount(['carUnits', 'reviews'])
            ->when($modelId > 0, fn ($query) => $query->where('model_id', $modelId))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return $this->adminView('admin.catalog.trims.index', [
            'adminPageTitle' => 'Catalog: Trims',
            'adminPageDescription' => 'Quan ly phien ban, spec chung, features va attributes theo model.',
            'catalogTab' => 'trims',
            'trims' => $trims,
            'models' => CarModel::query()->with('make')->orderBy('name')->get(),
            'catalogSearch' => $search,
            'selectedModelId' => $modelId,
        ]);
    }

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

    public function destroy(Trim $trimRecord): RedirectResponse
    {
        if ($trimRecord->carUnits()->exists() || $trimRecord->reviews()->exists()) {
            return back()->withErrors(['trim' => 'Khong the xoa trim da co inventory hoac review lien ket.']);
        }

        $trimRecord->delete();
        $this->pushSuccessToast('Da xoa trim.');

        return redirect()->route('admin.catalog.trims.index');
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
