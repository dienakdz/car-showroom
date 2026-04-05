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
                <h4>Quick create make</h4>
                <p>Table-first nhu CRM: tao nhanh o tren, thao tac chinh o bang duoi.</p>
            </div>
            <div class="admin-catalog-toolbar-meta">
                <span>{{ number_format($makes->total()) }} make</span>
                <span wire:loading wire:target="logoUpload">Dang tai logo...</span>
            </div>
        </div>

        <form wire:submit="create" class="admin-catalog-quick-form">
            <div class="admin-catalog-quick-form-grid admin-catalog-quick-form-grid-makes">
                <label class="admin-catalog-field">
                    <span>Ten hang xe</span>
                    <input type="text" wire:model.blur="createForm.name" placeholder="Toyota">
                    @error('createForm.name') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>Slug</span>
                    <input type="text" wire:model.blur="createForm.slug" placeholder="toyota">
                    @error('createForm.slug') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>Upload logo</span>
                    <input type="file" wire:model="logoUpload" accept=".png,.jpg,.jpeg,.svg,.webp">
                    <small>SVG, PNG, JPG, WebP. Luu vao public storage.</small>
                    @error('logoUpload') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>

                <label class="admin-catalog-field">
                    <span>URL / asset path</span>
                    <input type="text" wire:model.blur="createForm.manual_logo_path" placeholder="boxcar/images/brands/toyota.svg">
                    @error('createForm.manual_logo_path') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-catalog-form-actions">
                <div class="admin-catalog-form-note">Row moi se len dau khi sort theo updated, de de xac nhan logo va du lieu vua tao.</div>
                <button type="submit" class="admin-submit-btn">Tao make</button>
            </div>
        </form>
    </section>

    <section class="admin-catalog-panel">
        <div class="admin-catalog-panel-head">
            <div>
                <h4>Make directory</h4>
                <p>Search, sort, pagination va quick edit trong cung mot table view.</p>
            </div>
            <div class="admin-catalog-toolbar">
                <label class="admin-catalog-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Tim theo ten hoac slug">
                </label>
            </div>
        </div>

        <div class="admin-table-wrap admin-catalog-table-wrap">
            <table class="admin-table admin-catalog-data-table">
                <thead>
                <tr>
                    <th>
                        <button type="button" class="admin-table-sort" wire:click="sortBy('name')">
                            Thuong hieu
                            @if ($sortField === 'name')
                                <span>{{ $sortDirection === 'asc' ? '^' : 'v' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button type="button" class="admin-table-sort" wire:click="sortBy('slug')">
                            Slug
                            @if ($sortField === 'slug')
                                <span>{{ $sortDirection === 'asc' ? '^' : 'v' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button type="button" class="admin-table-sort" wire:click="sortBy('models_count')">
                            Models
                            @if ($sortField === 'models_count')
                                <span>{{ $sortDirection === 'asc' ? '^' : 'v' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Logo</th>
                    <th>
                        <button type="button" class="admin-table-sort" wire:click="sortBy('updated_at')">
                            Updated
                            @if ($sortField === 'updated_at')
                                <span>{{ $sortDirection === 'asc' ? '^' : 'v' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($makes as $make)
                    <tr wire:key="make-row-{{ $make->id }}">
                        <td>
                            <div class="admin-catalog-brand-cell">
                                <div class="admin-catalog-logo">
                                    @if ($make->logo_url)
                                        <img src="{{ $make->logo_url }}" alt="{{ $make->name }} logo">
                                    @else
                                        <span>{{ $make->initials }}</span>
                                    @endif
                                </div>
                                <div class="admin-catalog-brand-meta">
                                    <strong>{{ $make->name }}</strong>
                                    <div class="admin-table-subtext">ID #{{ $make->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="admin-catalog-chip">{{ $make->slug }}</span></td>
                        <td>{{ number_format($make->models_count) }}</td>
                        <td>
                            @if ($make->logo_url)
                                <span class="admin-catalog-status-badge">Dang hien thi</span>
                                <div class="admin-table-subtext">{{ \Illuminate\Support\Str::limit($make->logo_path ?? '', 44) }}</div>
                            @else
                                <span class="admin-catalog-status-badge is-muted">Chua co logo</span>
                            @endif
                        </td>
                        <td>{{ optional($make->updated_at)->format('d/m/Y H:i') ?: '--' }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <button type="button" class="admin-table-btn" wire:click="startEdit({{ $make->id }})">Sua nhanh</button>
                                <button type="button" class="admin-table-btn admin-table-btn-danger" wire:click="delete({{ $make->id }})" onclick="return confirm('Xac nhan xoa make nay?')">Xoa</button>
                            </div>
                        </td>
                    </tr>

                    @if ($editingId === $make->id)
                        <tr class="admin-catalog-edit-row" wire:key="make-editor-{{ $make->id }}">
                            <td colspan="6">
                                <form wire:submit="update" class="admin-catalog-row-editor">
                                    <div class="admin-catalog-quick-form-grid admin-catalog-quick-form-grid-makes">
                                        <label class="admin-catalog-field">
                                            <span>Ten make</span>
                                            <input type="text" wire:model.blur="editForm.name">
                                            @error('editForm.name') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>Slug</span>
                                            <input type="text" wire:model.blur="editForm.slug">
                                            @error('editForm.slug') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>Logo moi</span>
                                            <input type="file" wire:model="editLogoUpload" accept=".png,.jpg,.jpeg,.svg,.webp">
                                            @error('editLogoUpload') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="admin-catalog-field">
                                            <span>URL / asset path</span>
                                            <input type="text" wire:model.blur="editForm.manual_logo_path" placeholder="De trong de giu logo upload hien tai">
                                            @error('editForm.manual_logo_path') <small class="admin-catalog-error">{{ $message }}</small> @enderror
                                        </label>
                                    </div>

                                    <div class="admin-catalog-form-actions">
                                        <div class="admin-catalog-form-note" wire:loading wire:target="editLogoUpload">Dang tai logo moi...</div>
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
                            <div class="admin-empty-state">Chua co make nao phu hop bo loc hien tai.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-catalog-table-footer">
            <div class="admin-catalog-table-meta">
                @if ($makes->total() > 0)
                    <span>Hien thi {{ $makes->firstItem() }}-{{ $makes->lastItem() }} / {{ number_format($makes->total()) }} make</span>
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
                {{ $makes->links() }}
            </div>
        </div>
    </section>
</div>
