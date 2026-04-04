<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\UpsertMakeRequest;
use App\Models\Make;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MakeController extends AdminBaseController
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $makes = Make::query()
            ->withCount('models')
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

        return $this->adminView('admin.catalog.makes.index', [
            'adminPageTitle' => 'Catalog: Makes',
            'adminPageDescription' => 'Quan ly hang xe va namespace slug cho module catalog.',
            'catalogTab' => 'makes',
            'makes' => $makes,
            'catalogSearch' => $search,
        ]);
    }

    public function store(UpsertMakeRequest $request): RedirectResponse
    {
        Make::query()->create($request->validated());
        $this->pushSuccessToast('Da tao make moi.');

        return redirect()->route('admin.catalog.makes.index');
    }

    public function update(UpsertMakeRequest $request, Make $make): RedirectResponse
    {
        $make->update($request->validated());
        $this->pushSuccessToast('Da cap nhat make.');

        return redirect()->route('admin.catalog.makes.index');
    }

    public function destroy(Make $make): RedirectResponse
    {
        if ($make->models()->exists()) {
            return back()->withErrors(['make' => 'Khong the xoa make da co model lien ket.']);
        }

        $make->delete();
        $this->pushSuccessToast('Da xoa make.');

        return redirect()->route('admin.catalog.makes.index');
    }
}
