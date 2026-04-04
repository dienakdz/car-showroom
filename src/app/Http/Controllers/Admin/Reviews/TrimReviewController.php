<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Reviews\UpdateTrimReviewRequest;
use App\Models\TrimReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrimReviewController extends AdminBaseController
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->string('status'));

        $reviews = TrimReview::query()
            ->with(['trim.model.make', 'user:id,name'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return $this->adminView('admin.reviews.index', [
            'adminPageTitle' => 'Review moderation',
            'adminPageDescription' => 'Duyet review theo trim de hien thi tren public site.',
            'reviews' => $reviews,
            'selectedStatus' => $status,
        ]);
    }

    public function update(UpdateTrimReviewRequest $request, TrimReview $trimReview): RedirectResponse
    {
        $trimReview->update($request->validated());
        $this->pushSuccessToast('Da cap nhat trang thai review.');

        return redirect()->route('admin.reviews.index');
    }
}
