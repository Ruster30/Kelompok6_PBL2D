@props(['name', 'label', 'value' => '', 'placeholder' => '', 'required' => false, 'disabled' => false, 'rows' => 4])

@php
    $hasError = $errors->has($name);
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required) <span class="text-red">*</span> @endif
        </label>
    @endif
    
    <textarea name="{{ $name }}" 
              id="{{ $name }}" 
              placeholder="{{ $placeholder }}"
              rows="{{ $rows }}"
              class="form-input @if($hasError) error @endif"
              @if($required) required @endif
              @if($disabled) disabled @endif
              {{ $attributes }}>{{ old($name, $value) }}</textarea>
    
    @error($name)
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
