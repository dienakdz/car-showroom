@extends('admin.layouts.app')

@section('title', $trimRecord->exists ? 'Cap nhat Trim' : 'Tao Trim')

@section('page-actions')
    <a href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}" class="admin-action-btn admin-action-btn-secondary">Ve workspace catalog</a>
@endsection

@section('admin-content')
    @php($attributeValueModels = $trimRecord->relationLoaded('attributeValues') ? $trimRecord->attributeValues->keyBy('attribute_id') : collect())
    @php($selectedFeatureIds = collect(old('feature_ids', $trimRecord->exists ? $trimRecord->features->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all())

    @include('admin.catalog._nav')

    <form action="{{ $trimRecord->exists ? route('admin.catalog.trims.update', $trimRecord) : route('admin.catalog.trims.store') }}" method="POST">
        @csrf
        @if ($trimRecord->exists)
            @method('PATCH')
        @endif

        <div class="form-box admin-template-form-box admin-form-tabs-shell">
            <ul class="nav nav-tabs" id="trim-form-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="trim-basics-tab" data-bs-toggle="tab" data-bs-target="#trim-basics" type="button" role="tab" aria-controls="trim-basics" aria-selected="true">
                        Trim details
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="trim-features-tab" data-bs-toggle="tab" data-bs-target="#trim-features" type="button" role="tab" aria-controls="trim-features" aria-selected="false">
                        Feature groups
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="trim-attributes-tab" data-bs-toggle="tab" data-bs-target="#trim-attributes" type="button" role="tab" aria-controls="trim-attributes" aria-selected="false">
                        Attributes
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="trim-form-tabs-content">
                <div class="tab-pane fade show active" id="trim-basics" role="tabpanel" aria-labelledby="trim-basics-tab">
                    <div class="row">
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Model</label>
                                <select name="model_id" required>
                                    <option value="">Chon model</option>
                                    @foreach ($models as $model)
                                        <option value="{{ $model->id }}" @selected((string) old('model_id', $trimRecord->model_id) === (string) $model->id)>
                                            {{ $model->make?->name }} / {{ $model->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label>Ten trim</label>
                                <input type="text" name="name" value="{{ old('name', $trimRecord->name) }}" required>
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Slug</label>
                                <input type="text" name="slug" value="{{ old('slug', $trimRecord->slug) }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>MSRP</label>
                                <input type="number" min="0" name="msrp" value="{{ old('msrp', $trimRecord->msrp) }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Year from</label>
                                <input type="number" min="1900" max="{{ now()->addYear()->format('Y') }}" name="year_from" value="{{ old('year_from', $trimRecord->year_from) }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label>Year to</label>
                                <input type="number" min="1900" max="{{ now()->addYear()->format('Y') }}" name="year_to" value="{{ old('year_to', $trimRecord->year_to) }}">
                            </div>
                        </div>
                        <div class="form-column col-lg-12">
                            <div class="form_boxes">
                                <label>Description</label>
                                <textarea name="description" rows="7" placeholder="Mo ta chung cho trim">{{ old('description', $trimRecord->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="trim-features" role="tabpanel" aria-labelledby="trim-features-tab">
                    <div class="admin-feature-groups">
                        @foreach ($featureGroups as $featureGroup)
                            <div class="admin-feature-group">
                                <h5>{{ $featureGroup->name }}</h5>
                                <div class="admin-checkbox-grid">
                                    @foreach ($featureGroup->features as $feature)
                                        <label class="admin-check-chip">
                                            <input
                                                type="checkbox"
                                                name="feature_ids[]"
                                                value="{{ $feature->id }}"
                                                @checked(in_array($feature->id, $selectedFeatureIds, true))
                                            >
                                            <span>{{ $feature->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade" id="trim-attributes" role="tabpanel" aria-labelledby="trim-attributes-tab">
                    <div class="row">
                        @foreach ($attributes as $attribute)
                            @php($attributeValue = $attributeValueModels->get($attribute->id))
                            <div class="form-column col-lg-6">
                                <div class="form_boxes">
                                    <label>{{ $attribute->label }}</label>

                                    @if ($attribute->type === 'string')
                                        <input
                                            type="text"
                                            name="attributes[{{ $attribute->id }}][value_string]"
                                            value="{{ old('attributes.' . $attribute->id . '.value_string', $attributeValue?->value_string) }}"
                                            placeholder="{{ $attribute->code }}"
                                        >
                                    @elseif ($attribute->type === 'number')
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="attributes[{{ $attribute->id }}][value_number]"
                                            value="{{ old('attributes.' . $attribute->id . '.value_number', $attributeValue?->value_number) }}"
                                            placeholder="{{ $attribute->unit ?: 'Gia tri so' }}"
                                        >
                                    @else
                                        <select name="attributes[{{ $attribute->id }}][value_boolean]">
                                            <option value="">Chua khai bao</option>
                                            <option value="1" @selected((string) old('attributes.' . $attribute->id . '.value_boolean', $attributeValue?->value_boolean === null ? '' : (int) $attributeValue->value_boolean) === '1')>Co</option>
                                            <option value="0" @selected((string) old('attributes.' . $attribute->id . '.value_boolean', $attributeValue?->value_boolean === null ? '' : (int) $attributeValue->value_boolean) === '0')>Khong</option>
                                        </select>
                                    @endif

                                    <small>{{ $attribute->code }}{{ $attribute->unit ? ' | ' . $attribute->unit : '' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-submit admin-form-submit-end">
                <button type="submit" class="theme-btn btn-style-one">
                    <span>{{ $trimRecord->exists ? 'Cap nhat trim' : 'Tao trim' }}</span>
                    <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow">
                </button>
            </div>
        </div>
    </form>
@endsection
