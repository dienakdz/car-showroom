<?php

namespace App\Services\Admin;

use App\Models\TrimReview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewManagementService
{
    /**
     * @return LengthAwarePaginator<int, TrimReview>
     */
    public function getPaginatedReviews(
        ?string $status = null,
        ?string $search = null,
        ?int $rating = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = TrimReview::query()
            ->with(['trim.model.make', 'user:id,name,email,phone'])
            ->latest('id');

        if ($status !== null && $status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($rating !== null && $rating > 0) {
            $query->where('rating', $rating);
        }

        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('comment', 'like', $term)
                    ->orWhereHas('user', function (Builder $userQuery) use ($term): void {
                        $userQuery->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term)
                            ->orWhere('phone', 'like', $term);
                    })
                    ->orWhereHas('trim', function (Builder $trimQuery) use ($term): void {
                        $trimQuery->where('name', 'like', $term)
                            ->orWhereHas('model', function (Builder $modelQuery) use ($term): void {
                                $modelQuery->where('name', 'like', $term)
                                    ->orWhereHas('make', function (Builder $makeQuery) use ($term): void {
                                        $makeQuery->where('name', 'like', $term);
                                    });
                            });
                    });
            });
        }

        return $query->paginate($perPage, ['*'], 'reviewsPage');
    }

    /**
     * @return array{total: int, pending: int, approved: int, hidden: int, avg_rating: float}
     */
    public function getStats(): array
    {
        $total = TrimReview::query()->count();
        $pending = TrimReview::query()->where('status', 'pending')->count();
        $approved = TrimReview::query()->where('status', 'approved')->count();
        $hidden = TrimReview::query()->where('status', 'hidden')->count();

        $avg = TrimReview::query()->where('status', 'approved')->avg('rating');
        if ($avg === null && $total > 0) {
            $avg = TrimReview::query()->avg('rating');
        }

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'hidden' => $hidden,
            'avg_rating' => round((float) ($avg ?? 5.0), 1),
        ];
    }

    public function updateStatus(int $reviewId, string $status): bool
    {
        if (! in_array($status, ['pending', 'approved', 'hidden'], true)) {
            return false;
        }

        $review = TrimReview::query()->find($reviewId);
        if (! $review instanceof TrimReview) {
            return false;
        }

        return $review->update(['status' => $status]);
    }

    public function deleteReview(int $reviewId): bool
    {
        $review = TrimReview::query()->find($reviewId);
        if (! $review instanceof TrimReview) {
            return false;
        }

        return (bool) $review->delete();
    }
}
