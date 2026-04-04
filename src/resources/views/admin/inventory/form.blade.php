@extends('admin.layouts.app')

@section('title', $carUnit->exists ? 'Cap nhat Inventory' : 'Them Inventory')

@section('page-actions')
    <a href="{{ route('admin.inventory.index') }}" class="admin-action-btn admin-action-btn-secondary">Ve inventory</a>
@endsection

@section('admin-content')
    @php
        $mediaRows = old('media', $carUnit->exists
            ? $carUnit->media->map(fn ($media) => [
                'id' => $media->id,
                'type' => $media->type,
                'path_or_url' => $media->path_or_url,
                'caption' => $media->caption,
                'sort_order' => $media->sort_order,
                'is_cover' => $media->is_cover,
            ])->all()
            : [[
                'type' => 'image',
                'path_or_url' => '',
                'caption' => '',
                'sort_order' => 0,
                'is_cover' => true,
            ]]);
    @endphp

    <form action="{{ $carUnit->exists ? route('admin.inventory.update', $carUnit) : route('admin.inventory.store') }}" method="POST">
        @csrf
        @if ($carUnit->exists)
            @method('PATCH')
        @endif

        <div class="form-box admin-template-form-box admin-form-tabs-shell">
            <ul class="nav nav-tabs" id="inventory-form-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="inventory-main-tab" data-bs-toggle="tab" data-bs-target="#inventory-main" type="button" role="tab" aria-controls="inventory-main" aria-selected="true">
                        Vehicle info
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inventory-specs-tab" data-bs-toggle="tab" data-bs-target="#inventory-specs" type="button" role="tab" aria-controls="inventory-specs" aria-selected="false">
                        Specs
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inventory-media-tab" data-bs-toggle="tab" data-bs-target="#inventory-media" type="button" role="tab" aria-controls="inventory-media" aria-selected="false">
                        Media
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="inventory-form-tabs-content">
                <div class="tab-pane fade show active" id="inventory-main" role="tabpanel" aria-labelledby="inventory-main-tab">
                    <div class="row">
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Trim</label>
                                <select name="trim_id" required>
                                    <option value="">Chon trim</option>
                                    @foreach ($trims as $trim)
                                        <option value="{{ $trim->id }}" @selected((string) old('trim_id', $carUnit->trim_id) === (string) $trim->id)>
                                            {{ $trim->model?->make?->name }} / {{ $trim->model?->name }} / {{ $trim->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-3">
                            <div class="form_boxes">
                                <label>Condition</label>
                                <select name="condition" required>
                                    @foreach (['new', 'used', 'cpo'] as $condition)
                                        <option value="{{ $condition }}" @selected(old('condition', $carUnit->condition ?: 'new') === $condition)>{{ strtoupper($condition) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-3">
                            <div class="form_boxes">
                                <label>Status</label>
                                <select name="status" required>
                                    @php($statusOptions = ['draft', 'available', 'archived'])
                                    @if ($carUnit->status === 'on_hold')
                                        @php($statusOptions[] = 'on_hold')
                                    @endif
                                    @if ($carUnit->status === 'sold')
                                        @php($statusOptions[] = 'sold')
                                    @endif
                                    @foreach ($statusOptions as $status)
                                        <option value="{{ $status }}" @selected(old('status', $carUnit->status ?: 'draft') === $status)>{{ strtoupper($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Stock code</label>
                                <input type="text" name="stock_code" value="{{ old('stock_code', $carUnit->stock_code) }}" required>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>VIN</label>
                                <input type="text" name="vin" value="{{ old('vin', $carUnit->vin) }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-2">
                            <div class="form_boxes">
                                <label>Year</label>
                                <input type="number" min="1900" max="{{ now()->addYear()->format('Y') }}" name="year" value="{{ old('year', $carUnit->year ?: now()->format('Y')) }}" required>
                            </div>
                        </div>
                        <div class="form-column col-lg-2">
                            <div class="form_boxes">
                                <label>Mileage</label>
                                <input type="number" min="0" name="mileage" value="{{ old('mileage', $carUnit->mileage) }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Price</label>
                                <input type="number" min="0" name="price" value="{{ old('price', $carUnit->price) }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-2">
                            <div class="form_boxes">
                                <label>Currency</label>
                                <input type="text" name="currency" value="{{ old('currency', $carUnit->currency ?: 'VND') }}" maxlength="3" required>
                            </div>
                        </div>
                        <div class="form-column col-lg-12">
                            <div class="form_boxes">
                                <label>Ghi chu noi bo</label>
                                <textarea name="notes_internal" rows="5">{{ old('notes_internal', $carUnit->notes_internal) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="inventory-specs" role="tabpanel" aria-labelledby="inventory-specs-tab">
                    <div class="row">
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Body type</label>
                                <select name="body_type_id">
                                    <option value="">Chon</option>
                                    @foreach ($bodyTypes as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('body_type_id', $carUnit->body_type_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Fuel type</label>
                                <select name="fuel_type_id">
                                    <option value="">Chon</option>
                                    @foreach ($fuelTypes as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('fuel_type_id', $carUnit->fuel_type_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Transmission</label>
                                <select name="transmission_id">
                                    <option value="">Chon</option>
                                    @foreach ($transmissions as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('transmission_id', $carUnit->transmission_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Drivetrain</label>
                                <select name="drivetrain_id">
                                    <option value="">Chon</option>
                                    @foreach ($drivetrains as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('drivetrain_id', $carUnit->drivetrain_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Exterior color</label>
                                <select name="exterior_color_id">
                                    <option value="">Chon</option>
                                    @foreach ($colors as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('exterior_color_id', $carUnit->exterior_color_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Interior color</label>
                                <select name="interior_color_id">
                                    <option value="">Chon</option>
                                    @foreach ($colors as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('interior_color_id', $carUnit->interior_color_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="inventory-media" role="tabpanel" aria-labelledby="inventory-media-tab">
                    <div class="admin-template-section-head">
                        <div>
                            <h5>Media rows</h5>
                            <p>Quan ly cover image, gallery va video nguon cho listing.</p>
                        </div>
                        <button type="button" class="admin-table-btn" id="add-media-row">Them dong</button>
                    </div>

                    <div class="my-listing-table wrap-listing admin-listing-shell admin-inline-table-shell">
                        <div class="cart-table">
                            <table id="media-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Path / URL</th>
                                        <th>Caption</th>
                                        <th>Sort</th>
                                        <th>Cover</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mediaRows as $index => $mediaRow)
                                        <tr class="media-row">
                                            <td>
                                                <input type="hidden" name="media[{{ $index }}][id]" value="{{ $mediaRow['id'] ?? '' }}">
                                                <select name="media[{{ $index }}][type]">
                                                    @foreach (['image', 'video'] as $type)
                                                        <option value="{{ $type }}" @selected(($mediaRow['type'] ?? 'image') === $type)>{{ strtoupper($type) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="media[{{ $index }}][path_or_url]" value="{{ $mediaRow['path_or_url'] ?? '' }}" placeholder="https://... hoac boxcar/images/..."></td>
                                            <td><input type="text" name="media[{{ $index }}][caption]" value="{{ $mediaRow['caption'] ?? '' }}"></td>
                                            <td><input type="number" min="0" name="media[{{ $index }}][sort_order]" value="{{ $mediaRow['sort_order'] ?? $index }}"></td>
                                            <td class="admin-inline-checkbox-cell">
                                                <input type="hidden" name="media[{{ $index }}][is_cover]" value="0">
                                                <input type="checkbox" name="media[{{ $index }}][is_cover]" value="1" {{ !empty($mediaRow['is_cover']) ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-submit admin-form-submit-end">
                <button type="submit" class="theme-btn btn-style-one">
                    <span>{{ $carUnit->exists ? 'Cap nhat inventory item' : 'Tao inventory item' }}</span>
                    <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow">
                </button>
            </div>
        </div>
    </form>

    @if ($carUnit->exists)
        <div class="row admin-dashboard-grids">
            <div class="col-xl-4">
                <div class="right-box-three admin-side-box">
                    <h6 class="title">Listing meta</h6>
                    <div class="admin-meta-list">
                        <div><strong>Published:</strong> {{ optional($carUnit->published_at)->format('d/m/Y H:i') ?? 'Chua publish' }}</div>
                        <div><strong>Hold until:</strong> {{ optional($carUnit->hold_until)->format('d/m/Y H:i') ?? 'Khong giu' }}</div>
                        <div><strong>Sold at:</strong> {{ optional($carUnit->sold_at)->format('d/m/Y H:i') ?? 'Chua sold' }}</div>
                    </div>
                </div>

                <div class="right-box-three admin-side-box mt-4">
                    <h6 class="title">Workflow actions</h6>
                    <div class="admin-workflow-stack">
                        @if ($carUnit->status !== 'available' && $carUnit->status !== 'sold')
                            <form action="{{ route('admin.inventory.publish', $carUnit) }}" method="POST">
                                @csrf
                                <button type="submit" class="admin-submit-btn admin-submit-btn-full">Publish listing</button>
                            </form>
                        @endif

                        @if ($carUnit->status !== 'archived')
                            <form action="{{ route('admin.inventory.archive', $carUnit) }}" method="POST">
                                @csrf
                                <button type="submit" class="admin-submit-btn admin-submit-btn-outline admin-submit-btn-full">Archive listing</button>
                            </form>
                        @endif

                        @if ($carUnit->status === 'on_hold')
                            <form action="{{ route('admin.inventory.hold.release', $carUnit) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-submit-btn admin-submit-btn-outline admin-submit-btn-full">Release hold</button>
                            </form>
                        @elseif ($carUnit->status !== 'sold' && $carUnit->status !== 'archived')
                            <form action="{{ route('admin.inventory.hold', $carUnit) }}" method="POST" class="admin-stack-form">
                                @csrf
                                <div class="form_boxes">
                                    <label>Hold until</label>
                                    <input type="datetime-local" name="hold_until" required>
                                </div>
                                <div class="form_boxes">
                                    <label>Reason</label>
                                    <textarea name="reason" rows="3" placeholder="Ly do giu xe"></textarea>
                                </div>
                                <button type="submit" class="admin-submit-btn admin-submit-btn-full">Dat hold</button>
                            </form>
                        @endif

                        <form action="{{ route('admin.inventory.price.update', $carUnit) }}" method="POST" class="admin-stack-form">
                            @csrf
                            <div class="form_boxes">
                                <label>Cap nhat gia</label>
                                <input type="number" min="0" name="price" value="{{ $carUnit->price }}">
                            </div>
                            <button type="submit" class="admin-submit-btn admin-submit-btn-full">Luu gia</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="right-box-three admin-side-box">
                    <h6 class="title">Hold history</h6>
                    <div class="admin-list-stack">
                        @forelse ($carUnit->holds->sortByDesc('hold_until')->take(5) as $hold)
                            <div class="admin-list-item">
                                <div>
                                    <strong>{{ optional($hold->hold_until)->format('d/m/Y H:i') }}</strong>
                                    <p>{{ $hold->reason ?: 'Khong co ly do' }}</p>
                                </div>
                                <div class="admin-list-meta">
                                    <small>{{ $hold->createdBy?->name ?? 'Staff' }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty-state">Chua co lich su hold.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="right-box-three admin-side-box">
                    <h6 class="title">Price history</h6>
                    <div class="admin-list-stack">
                        @forelse ($carUnit->priceHistories->sortByDesc('created_at')->take(5) as $priceHistory)
                            <div class="admin-list-item">
                                <div>
                                    <strong>{{ number_format($priceHistory->old_price ?? 0, 0, ',', '.') }} -> {{ number_format($priceHistory->new_price ?? 0, 0, ',', '.') }} {{ $carUnit->currency }}</strong>
                                    <p>{{ optional($priceHistory->created_at)->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="admin-list-meta">
                                    <small>{{ $priceHistory->changedBy?->name ?? 'Staff' }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty-state">Chua co lich su doi gia.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#media-table tbody');
            const addButton = document.getElementById('add-media-row');

            if (!tableBody || !addButton) {
                return;
            }

            addButton.addEventListener('click', function () {
                const index = tableBody.querySelectorAll('.media-row').length;
                const row = document.createElement('tr');
                row.className = 'media-row';
                row.innerHTML = `
                    <td>
                        <input type="hidden" name="media[${index}][id]" value="">
                        <select name="media[${index}][type]">
                            <option value="image">IMAGE</option>
                            <option value="video">VIDEO</option>
                        </select>
                    </td>
                    <td><input type="text" name="media[${index}][path_or_url]" value="" placeholder="https://... hoac boxcar/images/..."></td>
                    <td><input type="text" name="media[${index}][caption]" value=""></td>
                    <td><input type="number" min="0" name="media[${index}][sort_order]" value="${index}"></td>
                    <td class="admin-inline-checkbox-cell">
                        <input type="hidden" name="media[${index}][is_cover]" value="0">
                        <input type="checkbox" name="media[${index}][is_cover]" value="1">
                    </td>
                `;
                tableBody.appendChild(row);
            });
        });
    </script>
@endpush
