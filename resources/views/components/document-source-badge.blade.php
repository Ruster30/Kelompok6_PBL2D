@props(['source'])

<span class="badge {{ $source->badge() }}">
    {{ $source->label() }}
</span>