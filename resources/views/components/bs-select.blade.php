@props(['name', 'label', 'options' => [], 'value' => '', 'placeholder' => '-- Pilih --', 'required' => false, 'disabled' => false])

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
    
    <select name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-input @if($hasError) error @endif"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}>
        
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $optionValue => $optionLabel)
            @if(is_string($optionValue))
                <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @else
                <option value="{{ $optionLabel }}" {{ old($name, $value) == $optionLabel ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endif
        @endforeach
    </select>
    
    @error($name)
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
