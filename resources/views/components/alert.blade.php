@props(['type' => 'success', 'message' => null])

@php
    $message = $message ?? session($type);
    
    if ($type === 'success') {
        $icon = 'bi-check-circle-fill';
    } elseif ($type === 'error') {
        $icon = 'bi-exclamation-circle-fill';
    } else {
        $icon = 'bi-info-circle-fill';
    }
@endphp

@if($message)
    <div id="{{ $type }}-alert" class="alert-box alert-{{ $type }}">
        <i class="bi {{ $icon }}" aria-hidden="true"></i>
        {{ $message }}
    </div>
@endif
