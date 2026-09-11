<?php

namespace App\Livewire\Admin\Reviews;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\TrimReview;
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

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function updateStatus(int $reviewId, string $status): void
    {
        $validated = validator(
            ['status' => $status],
            ['status' => ['required', Rule::in(self::STATUSES)]],
        )->validate();

        TrimReview::query()->findOrFail($reviewId)->update([
            'status' => $validated['status'],
        ]);

        $this->feedback = [
            'type' => 'success',
            'message' => 'Da cap nhat trang thai review.',
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.reviews.index', [
            'statuses' => self::STATUSES,
            'reviews' => TrimReview::query()
                ->with(['trim.model.make', 'user:id,name'])
                ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
                ->latest()
                ->paginate(12, ['*'], 'reviewsPage'),
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Review moderation',
            'adminPageDescription' => 'Duyet review theo trim de hien thi tren public site.',
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
