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
                <h4>Quick create trim</h4>
                <p>CRUD nhanh trong workspace, feature groups va attributes de o form chi tiet.</p>
            </div>
            <a href="{{ route('admin.catalog.trims.create') }}" class="admin-catalog-inline-link">Mo form day du</a>
        </div>

        <form wire:submit="create" class="admin-catalog-quick-form">
            <div class="admin-catalog-quick-form-grid admin-catalog-quick-form-grid-trims">
                <label class="admin-catalog-field">
                    <span>Model</span>
                    <select wire:model.blur="createForm.model_id">
                        <option value="">Chon model</option>
                        @foreach ($modelOptions as $modelOption)
                            <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
                        @endforeach
                    </select>
                    @error('createForm.model_id') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>Ten trim</span>
                    <input type="text" wire:model.blur="createForm.name" placeholder="RS">
                    @error('createForm.name') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>Slug</span>
                    <input type="text" wire:model.blur="createForm.slug" placeholder="rs">
                    @error('createForm.slug') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>Year from</span>
                    <input type="number" wire:model.blur="createForm.year_from" min="1900" max="{{ now()->addYear()->format('Y') }}">
                    @error('createForm.year_from') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>Year to</span>
                    <input type="number" wire:model.blur="createForm.year_to" min="1900" max="{{ now()->addYear()->format('Y') }}">
                    @error('createForm.year_to') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>MSRP</span>
                    <input type="number" wire:model.blur="createForm.msrp" min="0" placeholder="890000000">
                    @error('createForm.msrp') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field admin-catalog-field-span-2">
                    <span>Mo ta ngan</span>
                    <textarea wire:model.blur="createForm.description" rows="4" placeholder="Mo ta chung cho trim"></textarea>
                    @error('createForm.description') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-catalog-form-actions">
                <div class="admin-catalog-form-note">Quick create de phu hop workflow catalog hang ngay; chi tiet chuyen qua man hinh rieng.</div>
                <button type="submit" class="admin-submit-btn">Tao trim</button>
            </div>
        </form>
    </section>

    <section class="admin-catalog-panel">
        <div class="admin-catalog-panel-head">
            <div>
                <h4>Trim directory</h4>
                <p>Table view de quan sat trim, year range, gia va usage trong mot tam nhin.</p>
            </div>
            <div class="admin-catalog-toolbar admin-catalog-toolbar-double">
                <label class="admin-catalog-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Tim trim, slug hoac mo ta">
                </label>
                <label class="admin-catalog-select">
                    <select wire:model.live="modelFilter">
                        <option value="">Tat ca model</option>
                        @foreach ($modelOptions as $modelOption)
                            <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>

        <div class="admin-table-wrap admin-catalog-table-wrap">
            <table class="admin-table admin-catalog-data-table">
                <thead>
                <tr>
                    <th>Trim</th>
                    <th>Model</th>
                    <th>Years</th>
                    <th>MSRP</th>
                    <th>Usage</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($trims as $trim)
                    <tr wire:key="trim-row-{{ $trim->id }}">
                        <td>
                            <div class="admin-catalog-brand-cell">
                                <div class="admin-catalog-logo">
                                    <span>{{ strtoupper(substr($trim->name, 0, 1)) }}</span>
                                </div>
                                <div class="admin-catalog-brand-meta">
                                    <strong>{{ $trim->name }}</strong>
                                    <div class="admin-table-subtext">{{ $trim->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $trim->model?->make?->name }} / {{ $trim->model?->name }}</td>
                        <td>{{ $trim->year_from ?: '...' }} - {{ $trim->year_to ?: 'Nay' }}</td>
                        <td>{{ $trim->msrp ? number_format((int) $trim->msrp, 0, ',', '.') . ' VND' : 'Chua co MSRP' }}</td>
                        <td>
                            <span class="admin-catalog-chip">{{ $trim->car_units_count }} inventory</span>
                            <span class="admin-catalog-chip">{{ $trim->reviews_count }} reviews</span>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a href="{{ route('admin.catalog.trims.edit', $trim) }}" class="admin-table-btn">Chi tiet</a>
                                <button type="button" class="admin-table-btn" wire:click="startEdit({{ $trim->id }})">Sua nhanh</button>
                                <button type="button" class="admin-table-btn admin-table-btn-danger" wire:click="delete({{ $trim->id }})" onclick="return confirm('Xac nhan xoa trim nay?')">Xoa</button>
                            </div>
                        </td>
                    </tr>

                    @if ($editingId === $trim->id)
                        <tr class="admin-catalog-edit-row" wire:key="trim-editor-{{ $trim->id }}">
                            <td colspan="6">
                                <form wire:submit="update" class="admin-catalog-row-editor">
                                    <div class="admin-catalog-quick-form-grid admin-catalog-quick-form-grid-trims">
                                        <label class="admin-catalog-field">
                                            <span>Model</span>
                                            <select wire:model.blur="editForm.model_id">
                                                @foreach ($modelOptions as $modelOption)
                                                    <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editForm.model_id') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>Ten trim</span>
                                            <input type="text" wire:model.blur="editForm.name">
                                            @error('editForm.name') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>Slug</span>
                                            <input type="text" wire:model.blur="editForm.slug">
                                            @error('editForm.slug') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>Year from</span>
                                            <input type="number" wire:model.blur="editForm.year_from" min="1900" max="{{ now()->addYear()->format('Y') }}">
                                            @error('editForm.year_from') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>Year to</span>
                                            <input type="number" wire:model.blur="editForm.year_to" min="1900" max="{{ now()->addYear()->format('Y') }}">
                                            @error('editForm.year_to') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>MSRP</span>
                                            <input type="number" wire:model.blur="editForm.msrp" min="0">
                                            @error('editForm.msrp') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field admin-catalog-field-span-2">
                                            <span>Mo ta ngan</span>
                                            <textarea wire:model.blur="editForm.description" rows="4"></textarea>
                                            @error('editForm.description') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>
                                    </div>

                                    <div class="admin-catalog-form-actions">
                                        <div class="admin-catalog-form-note">Quick edit cho metadata co ban, con feature va attribute van nam o form chi tiet.</div>
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
                            <div class="admin-empty-state">Chua co trim nao phu hop bo loc hien tai.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-catalog-table-footer">
            <div class="admin-catalog-table-meta">
                @if ($trims->total() > 0)
                    <span>Hien thi {{ $trims->firstItem() }}-{{ $trims->lastItem() }} / {{ number_format($trims->total()) }} trim</span>
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
                {{ $trims->links() }}
            </div>
        </div>
    </section>
</div>
