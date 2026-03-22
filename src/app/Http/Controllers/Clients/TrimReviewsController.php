<?php

namespace App\Http\Controllers\Clients;

use App\Models\Trim;
use App\Models\TrimReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrimReviewsController extends ClientBaseController
{
    public function store(Request $request, string $trimSlug): RedirectResponse
    {
        $trim = $request->attributes->get('review_trim');
        if (! $trim instanceof Trim) {
            $trim = Trim::query()->where('slug', $trimSlug)->firstOrFail();
        }

        $user = $request->user();
        abort_if($user === null, 403);

        $existingReview = TrimReview::query()
            ->where('trim_id', $trim->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($existingReview) {
            return redirect()
                ->route('trim.show', ['trimSlug' => $trim->slug])
                ->withErrors(['review' => 'Ban da gui danh gia cho phien ban nay.']);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        TrimReview::query()->create([
            'trim_id' => $trim->id,
            'user_id' => $user->id,
            'rating' => (int) $data['rating'],
            'comment' => trim($data['comment']),
            'status' => 'pending',
        ]);
        $this->pushSuccessToast('Danh gia cua ban da duoc gui va dang cho duyet.');

        return redirect()
            ->route('trim.show', ['trimSlug' => $trim->slug]);
    }
}
