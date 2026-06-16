@extends('layouts.vendor')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Jadwal Event</h1>

    <div class="bg-white rounded-lg shadow p-4">
        <p>Total Event: {{ $events->count() }}</p>
        <p>Event Terpilih: {{ $selectedEvent }}</p>
        <p>Total Jadwal: {{ $jadwal->count() }}</p>
    </div>

    <div class="mt-4">
        @forelse($jadwal as $item)
            <div class="border rounded p-3 mb-2">
                <h3>{{ $item->judul }}</h3>
                <p>{{ $item->tanggal }}</p>
                <p>{{ $item->deskripsi }}</p>
            </div>
        @empty
            <p>Belum ada jadwal.</p>
        @endforelse
    </div>
</div>
@endsection