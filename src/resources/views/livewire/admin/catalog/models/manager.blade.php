<div class="admin-catalog-stack">
    @if ($feedback !== [])
        <div class="admin-catalog-feedback {{ ($feedback['type'] ?? 'success') === 'error' ? 'is-error' : 'is-success' }}">
            <div>
                <strong>{{ ($feedback['type'] ?? 'success') === 'error' ? 'Can xu ly' : 'Cap nhat thanh cong' }}</strong>
                <p>{{ $feedback['message'] ?? '' }}</p>
            </div>
            <button type="button" wire:click="dismissFeedback">Dong</button>
        </div>
    @endif

    <section class="admin-catalog-panel">
        <div class="admin-catalog-panel-head admin-catalog-panel-head-tight">
            <div>
                <h4>Quick create model</h4>
                <p>Table-first: create nhanh o tren, danh sach CRM-style o duoi.</p>
            </div>
            <div class="admin-catalog-toolbar-meta">
                <span>{{ number_format($models->total()) }} model</span>
            </div>
        </div>

        <form wire:submit="create" class="admin-catalog-quick-form">
            <div class="admin-catalog-quick-form-grid admin-catalog-quick-form-grid-models">
                <label class="admin-catalog-field">
                    <span>Make</span>
                    <select wire:model.blur="createForm.make_id">
                        <option value="">Chon make</option>
                        @foreach ($makeOptions as $makeOption)
                            <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                        @endforeach
                    </select>
                    @error('createForm.make_id') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>Ten model</span>
                    <input type="text" wire:model.blur="createForm.name" placeholder="Corolla Cross">
                    @error('createForm.name') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>Slug</span>
                    <input type="text" wire:model.blur="createForm.slug" placeholder="corolla-cross">
                    @error('createForm.slug') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-catalog-form-actions">
                <div class="admin-catalog-form-note">Loc theo make giup scale du lieu tot hon khi catalog tang len.</div>
                <button type="submit" class="admin-submit-btn">Tao model</button>
            </div>
        </form>
    </section>

    <section class="admin-catalog-panel">
        <div class="admin-catalog-panel-head">
            <div>
                <h4>Model directory</h4>
                <p>Table view de xem make, slug va trim count trong mot tam nhin.</p>
            </div>
            <div class="admin-catalog-toolbar admin-catalog-toolbar-double">
                <label class="admin-catalog-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Tim model">
                </label>
                <label class="admin-catalog-select">
                    <select wire:model.live="makeFilter">
                        <option value="">Tat ca make</option>
                        @foreach ($makeOptions as $makeOption)
                            <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>

        <div class="admin-table-wrap admin-catalog-table-wrap">
            <table class="admin-table admin-catalog-data-table">
                <thead>
                <tr>
                    <th>Model</th>
                    <th>Make</th>
                    <th>Slug</th>
                    <th>Trims</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($models as $model)
                    <tr wire:key="model-row-{{ $model->id }}">
                        <td>
                            <div class="admin-catalog-brand-cell">
                                <div class="admin-catalog-logo">
                                    @if ($model->make?->logo_url)
                                        <img src="{{ $model->make->logo_url }}" alt="{{ $model->make->name }} logo">
                                    @else
                                        <span>{{ $model->make?->initials ?? strtoupper(substr($model->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="admin-catalog-brand-meta">
                                    <strong>{{ $model->name }}</strong>
                                    <div class="admin-table-subtext">ID #{{ $model->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $model->make?->name ?: '--' }}</td>
                        <td><span class="admin-catalog-chip">{{ $model->slug }}</span></td>
                        <td>{{ number_format($model->trims_count) }}</td>
                        <td>{{ optional($model->updated_at)->format('d/m/Y H:i') ?: '--' }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <button type="button" class="admin-table-btn" wire:click="startEdit({{ $model->id }})">Sua nhanh</button>
                                <button type="button" class="admin-table-btn admin-table-btn-danger" wire:click="delete({{ $model->id }})" onclick="return confirm('Xac nhan xoa model nay?')">Xoa</button>
                            </div>
                        </td>
                    </tr>

                    @if ($editingId === $model->id)
                        <tr class="admin-catalog-edit-row" wire:key="model-editor-{{ $model->id }}">
                            <td colspan="6">
                                <form wire:submit="update" class="admin-catalog-row-editor">
                                    <div class="admin-catalog-quick-form-grid admin-catalog-quick-form-grid-models">
                                        <label class="admin-catalog-field">
                                            <span>Make</span>
                                            <select wire:model.blur="editForm.make_id">
                                                @foreach ($makeOptions as $makeOption)
                                                    <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editForm.make_id') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>Ten model</span>
                                            <input type="text" wire:model.blur="editForm.name">
                                            @error('editForm.name') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>Slug</span>
                                            <input type="text" wire:model.blur="editForm.slug">
                                            @error('editForm.slug') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>
                                    </div>

                                    <div class="admin-catalog-form-actions">
                                        <div class="admin-catalog-form-note">Inline edit tranh phai qua form rieng neu chi can sua metadata co ban.</div>
                                        <div class="admin-table-actions">
                                            <button type="submit" class="admin-submit-btn">Luu thay doi</button>
                                            <button type="button" class="admin-table-btn" wire:click="cancelEdit">Huy</button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty-state">Chua co model nao phu hop bo loc hien tai.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-catalog-table-footer">
            <div class="admin-catalog-table-meta">
                @if ($models->total() > 0)
                    <span>Hien thi {{ $models->firstItem() }}-{{ $models->lastItem() }} / {{ number_format($models->total()) }} model</span>
                @else
                    <span>Khong co du lieu</span>
                @endif

                <label class="admin-catalog-footer-select">
                    <span>So dong</span>
                    <select wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </label>
            </div>

            <div class="admin-catalog-pagination">
                {{ $models->links() }}
            </div>
        </div>
    </section>
</div>
