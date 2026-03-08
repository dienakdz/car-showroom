@php
    $valueField = $valueField ?? 'id';
    $labelField = $labelField ?? 'name';
    $selectedValue = isset($selectedValue) ? (string) $selectedValue : '';
    $emptyLabel = $emptyLabel ?? $placeholder ?? 'All';
    $includeEmptyOption = $includeEmptyOption ?? true;
    $menuClasses = $menuClasses ?? '';
    $selectedOption = collect($options ?? [])->first(function ($option) use ($valueField, $selectedValue) {
        return (string) data_get($option, $valueField) === $selectedValue;
    });
    $selectedLabel = $selectedOption ? data_get($selectedOption, $labelField) : $emptyLabel;
@endphp

<div class="drop-menu {{ $menuClasses }}" @if(!empty($autoSubmit)) data-auto-submit="true" @endif>
    <div class="select">
        <span>{{ $selectedLabel }}</span>
        <i class="fa fa-angle-down"></i>
    </div>
    <input type="hidden" name="{{ $name }}" value="{{ $selectedValue }}">
    <ul class="dropdown" style="display: none;">
        @if ($includeEmptyOption)
            <li data-value="">{{ $emptyLabel }}</li>
        @endif
        @foreach ($options as $option)
            <li data-value="{{ data_get($option, $valueField) }}">{{ data_get($option, $labelField) }}</li>
        @endforeach
    </ul>
</div>
