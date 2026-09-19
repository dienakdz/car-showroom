<?php

namespace App\Livewire\Admin\Reviews;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\TrimReview;
use App\Services\Admin\ReviewManagementService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends AdminPageComponent
{
    use WithPagination;

    private const STATUSES = ['pending', 'approved', 'hidden'];

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'status', except: '')]
    public string $status = '';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'rating', except: 0)]
    public int $ratingFilter = 0;

    public ?int $selectedReviewId = null;

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(): void
    {
        $this->ensureValidStatus();
    }

    public function updatedStatus(): void
    {
        $this->ensureValidStatus();
        $this->resetPage('reviewsPage');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('reviewsPage');
    }

    public function updatedRatingFilter(): void
    {
        $this->resetPage('reviewsPage');
    }

    public function setStatus(string $status): void
    {
        $this->status = $status === 'all' ? '' : $status;
        $this->ensureValidStatus();
        $this->resetPage('reviewsPage');
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->ratingFilter = 0;
        $this->resetPage('reviewsPage');
    }

    public function viewReview(int $id): void
    {
        $this->selectedReviewId = $id;
    }

    public function closeReviewModal(): void
    {
        $this->selectedReviewId = null;
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function updateStatus(int $reviewId, string $status, ReviewManagementService $service): void
    {
        $validated = validator(
            ['status' => $status],
            ['status' => ['required', Rule::in(self::STATUSES)]],
        )->validate();

        $success = $service->updateStatus($reviewId, (string) $validated['status']);

        if ($success) {
            $this->feedback = [
                'type' => 'success',
                'message' => 'Đã cập nhật trạng thái đánh giá thành công.',
            ];
        } else {
            $this->feedback = [
                'type' => 'error',
                'message' => 'Không thể cập nhật trạng thái đánh giá.',
            ];
        }
    }

    public function approve(int $reviewId, ReviewManagementService $service): void
    {
        $this->updateStatus($reviewId, 'approved', $service);
    }

    public function hide(int $reviewId, ReviewManagementService $service): void
    {
        $this->updateStatus($reviewId, 'hidden', $service);
    }

    public function delete(int $reviewId, ReviewManagementService $service): void
    {
        $success = $service->deleteReview($reviewId);

        if ($success) {
            if ($this->selectedReviewId === $reviewId) {
                $this->selectedReviewId = null;
            }
            $this->feedback = [
                'type' => 'success',
                'message' => 'Đã xóa đánh giá thành công.',
            ];
        } else {
            $this->feedback = [
                'type' => 'error',
                'message' => 'Không thể xóa đánh giá này.',
            ];
        }
    }

    public function render(ReviewManagementService $service): View
    {
        $stats = $service->getStats();
        $reviews = $service->getPaginatedReviews(
            $this->status,
            $this->search,
            $this->ratingFilter > 0 ? $this->ratingFilter : null,
            10
        );

        $selectedReview = null;
        if ($this->selectedReviewId !== null) {
            $selectedReview = TrimReview::query()
                ->with(['trim.model.make', 'user'])
                ->find($this->selectedReviewId);
        }

        return view('livewire.admin.reviews.index', [
            'statuses' => self::STATUSES,
            'stats' => $stats,
            'reviews' => $reviews,
            'selectedReview' => $selectedReview,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Quản lý Đánh giá xe',
            'adminPageDescription' => 'Kiểm duyệt và quản lý nhận xét của khách hàng theo từng phiên bản xe.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'reviews.approve';
    }

    private function ensureValidStatus(): void
    {
        if ($this->status !== '' && ! in_array($this->status, self::STATUSES, true)) {
            $this->status = '';
        }
    }
}
