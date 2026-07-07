@extends('layouts.vendor')

@section('title', 'Event Saya')
@section('page-title', 'Event Saya')

@section('content')

<div class="section-card">

    <!-- SEARCH -->
    <div class="mb-4">
        <form method="GET" action="{{ route('vendor.event-saya') }}">
            <div style="max-width:600px;">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari event..."
                    class="form-control"
                >
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table custom-table align-middle mb-0">

            <thead>
                <tr>
                    <th>NAMA EVENT</th>
                    <th>PIC / KLIEN</th>
                    <th>TANGGAL</th>
                    <th>LOKASI</th>
                    <th>STATUS</th>
                </tr>
            </thead>

            <tbody>

                @forelse($events as $event)

                    <tr>

                        <td>
                            <strong>
                                {{ $event->nama_event }}
                            </strong>
                        </td>

                        <td>
                            {{ $event->client->name ?? '-' }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ $event->lokasi_event }}
                        </td>

                        <td>

                            @php
                                $badgeClass = match($event->status_event) {
                                    'selesai' => 'bg-success',
                                    'berjalan' => 'bg-primary',
                                    'diproses' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp

                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst($event->status_event) }}
                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            Belum ada event ditugaskan
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

</div>

@endsection
