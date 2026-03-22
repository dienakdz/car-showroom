<?php

namespace App\Http\Middleware;

use App\Models\Sale;
use App\Models\Trim;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePurchasedTrimForReview
{
    public function handle(Request $request, Closure $next): Response
    {
        $trimSlug = (string) $request->route('trimSlug');
        $trim = Trim::query()->where('slug', $trimSlug)->firstOrFail();

        $request->attributes->set('review_trim', $trim);

        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        if (Sale::hasBuyerPurchasedTrim($user->id, $trim->id)) {
            return $next($request);
        }

        return $this->deny($trim->slug);
    }

    protected function deny(string $trimSlug): RedirectResponse
    {
        return redirect()
            ->route('trim.show', ['trimSlug' => $trimSlug])
            ->withErrors(['review' => 'Chi khach da mua xe thuoc phien ban nay moi co the danh gia.']);
    }
}
