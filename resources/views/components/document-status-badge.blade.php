@props(['status'])

<span class="badge {{ $status->badge() }}">
    {{ $status->label() }}
</span>