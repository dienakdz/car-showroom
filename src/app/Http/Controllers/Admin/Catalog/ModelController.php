<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\UpsertCarModelRequest;
use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModelController extends AdminBaseController
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $makeId = $request->integer('make_id');

        $models = CarModel::query()
            ->with('make')
            ->withCount('trims')
            ->when($makeId > 0, fn ($query) => $query->where('make_id', $makeId))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return $this->adminView('admin.catalog.models.index', [
            'adminPageTitle' => 'Catalog: Models',
            'adminPageDescription' => 'Quan ly dong xe theo tung make va slug duy nhat trong moi hang.',
            'catalogTab' => 'models',
            'models' => $models,
            'makes' => Make::query()->orderBy('name')->get(),
            'catalogSearch' => $search,
            'selectedMakeId' => $makeId,
        ]);
    }

    public function store(UpsertCarModelRequest $request): RedirectResponse
    {
        CarModel::query()->create($request->validated());
        $this->pushSuccessToast('Da tao model moi.');

        return redirect()->route('admin.catalog.models.index');
    }

    public function update(UpsertCarModelRequest $request, CarModel $carModel): RedirectResponse
    {
        $carModel->update($request->validated());
        $this->pushSuccessToast('Da cap nhat model.');

        return redirect()->route('admin.catalog.models.index');
    }

    public function destroy(CarModel $carModel): RedirectResponse
    {
        if ($carModel->trims()->exists()) {
            return back()->withErrors(['model' => 'Khong the xoa model da co trim lien ket.']);
        }

        $carModel->delete();
        $this->pushSuccessToast('Da xoa model.');

        return redirect()->route('admin.catalog.models.index');
    }
}
