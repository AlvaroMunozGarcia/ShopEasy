{{-- e:\ProyectoDAW\ShopEasy\resources\views\components\admin\input-field.blade.php --}}
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'model' => null, // El modelo Eloquent para formularios de edición
    'options' => [], // Para selects: ['value' => 'Label']
    'rows' => 3, // Para textareas
    'helpText' => null,
    'multiple' => false, // Para select multiple
    'disabled' => false,
    'readonly' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'checked' => null, // Para checkboxes o radios (si se usa este componente)
    'accept' => null, // Para type="file"
])

@php
    $oldValue = old($name);
    $fieldValue = $oldValue ?? ($model ? data_get($model, $name) : $value);
    if ($type === 'password') $fieldValue = ''; // No rellenar contraseñas
    if ($type === 'file' && $model && data_get($model, $name)) {
        // No mostrar el nombre del archivo antiguo en el input file, pero podría mostrarse un preview
    }
@endphp

<div class="form-group">
    <label for="{{ $name }}">{{ $label }}@if($required)<span class="text-danger">*</span>@endif</label>

    @if($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" class="form-control @error($name) is-invalid @enderror" placeholder="{{ $placeholder }}" rows="{{ $rows }}" @if($required) required @endif @if($disabled) disabled @endif @if($readonly) readonly @endif>{{ $fieldValue }}</textarea>
    @elseif($type === 'select')
        <select name="{{ $name }}{{ $multiple ? '[]' : '' }}" id="{{ $name }}" class="form-control @error($name) is-invalid @enderror" @if($required) required @endif @if($multiple) multiple @endif @if($disabled) disabled @endif>
            @if($placeholder !== false && !$multiple)
            <option value="">{{ $placeholder ?: '-- Seleccione --' }}</option>
            @endif
            @foreach($options as $optionVal => $optionLabel)
                @php
                    $isSelected = false;
                    if ($multiple && is_array($fieldValue)) {
                        $isSelected = in_array((string)$optionVal, array_map('strval', $fieldValue));
                    } elseif (!is_array($fieldValue)) {
                        $isSelected = (string)$fieldValue === (string)$optionVal;
                    }
                @endphp
                <option value="{{ $optionVal }}" @if($isSelected) selected @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" class="form-control @error($name) is-invalid @enderror" value="{{ $type !== 'file' ? $fieldValue : '' }}" placeholder="{{ $placeholder }}" @if($required) required @endif @if($disabled) disabled @endif @if($readonly) readonly @endif @if($min !== null) min="{{ $min }}" @endif @if($max !== null) max="{{ $max }}" @endif @if($step !== null) step="{{ $step }}" @endif @if($type === 'checkbox' && $checked) checked @endif @if($type === 'file' && $accept) accept="{{ $accept }}" @endif>
    @endif

    @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>