@extends('layouts.admin')

@section('title', 'Detail Event')

@section('content')
<div class="card">
    <h2>{{ $event->nama_event }}</h2>

    <p>
        <strong>Client:</strong>
        {{ $event->client->name ?? '-' }}
    </p>

    <p>
        <strong>Tanggal:</strong>
        {{ $event->tanggal_event }}
    </p>

    <p>
        <strong>Status:</strong>
        {{ $event->status_event }}
    </p>
</div>
@endsection