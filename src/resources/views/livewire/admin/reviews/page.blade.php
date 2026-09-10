<div>
    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="my-listing-table wrap-listing admin-listing-shell">
        <div class="cart-table">
            <div class="title-listing">
                <div>
                    <h5>Review moderation</h5>
                    <div class="text">Kiem duyet review trim tu khach hang truoc khi hien thi public.</div>
                </div>
                <div class="admin-template-toolbar">
                    <div class="admin-filter-box">
                        <select wire:model.live="status" aria-label="Lọc trạng thái review">
                            <option value="">Tat ca status</option>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption }}">{{ strtoupper($statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="admin-table-btn" wire:loading wire:target="status">Dang loc...</span>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Trim</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr wire:key="review-row-{{ $review->id }}">
                                <td>
                                    <div class="shop-cart-product admin-plain-product">
                                        <div class="shop-product-cart-img admin-symbol-badge">
                                            {{ strtoupper(substr($review->trim?->name ?? 'R', 0, 1)) }}
                                        </div>
                                        <div class="shop-product-cart-info">
                                            <h3>{{ trim(collect([$review->trim?->model?->make?->name, $review->trim?->model?->name, $review->trim?->name])->filter()->implode(' ')) }}</h3>
                                            <p>{{ $review->user?->name ?? 'Khach hang' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span>{{ $review->user?->name ?? 'Khach hang' }}</span></td>
                                <td><span>{{ $review->rating }}/5</span></td>
                                <td><span>{{ $review->comment }}</span></td>
                                <td>
                                    <div class="admin-inline-form-grid admin-inline-form-grid-compact">
                                        <select
                                            wire:change="updateStatus({{ $review->id }}, $event.target.value)"
                                            wire:loading.attr="disabled"
                                            aria-label="Trạng thái review {{ $review->id }}"
                                        >
                                            @foreach ($statuses as $statusOption)
                                                <option value="{{ $statusOption }}" @selected($review->status === $statusOption)>{{ strtoupper($statusOption) }}</option>
                                            @endforeach
                                        </select>
                                        <div class="admin-inline-actions">
                                            <span class="text-muted small" wire:loading>Dang luu...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="admin-empty-state">Chua co review nao.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $reviews->links('admin.partials.pagination') }}
        </div>
    </div>
</div>
