@props(['name', 'label', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'disabled' => false])

@php
    $hasError = $errors->has($name);
    $oldValue = old($name, $value);
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required) <span class="text-red">*</span> @endif
        </label>
    @endif
    
    <input type="{{ $type }}" 
           name="{{ $name }}" 
           id="{{ $name }}" 
           value="{{ $oldValue }}"
           placeholder="{{ $placeholder }}"
           class="form-input @if($hasError) error @endif"
           @if($required) required @endif
           @if($disabled) disabled @endif
           {{ $attributes }}>
    
    @error($name)
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
