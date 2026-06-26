@extends('layouts.vendor')

@section('title', 'Jadwal')
@section('page-title', 'Jadwal')

@section('content')

<div class="section-card">

    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>
            <h2 class="section-card-title" style="font-size:40px;">
                Jadwal Event
            </h2>

            <p class="text-muted mt-2 mb-0">
                Pantau progress event yang Anda kerjakan (mode hanya-lihat).
            </p>
        </div>

        <form method="GET" action="{{ route('vendor.jadwal') }}">
            @if($events->count())
            <select
                name="event"
                class="form-select"
                onchange="this.form.submit()"
                style="width:360px;"
            >
                @foreach($events as $event)
                    <option
                        value="{{ $event->id }}"
                        {{ $selectedEvent == $event->id ? 'selected' : '' }}
                    >
                        {{ $event->nama_event }}
                    </option>
                @endforeach
            </select>
            @endif
        </form>

    </div>

    <div class="section-card" style="min-height:400px;">

        @if(!$events->count())

            <div
                class="d-flex justify-content-center align-items-center"
                style="height:300px;"
            >
                <h4 class="text-muted fw-normal">
                    Anda belum ditugaskan ke event apapun.
                </h4>
            </div>

        @elseif($jadwal->count())

            <div class="timeline-wrapper">

                <div class="timeline-line"></div>

                @foreach($jadwal as $item)

                    <div class="timeline-item">

                        <div class="timeline-dot active"></div>

                        <div class="timeline-card">

                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <h5 class="mb-0 fw-semibold">
                                    {{ $item->judul }}
                                </h5>

                                <span class="timeline-date">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </span>

                            </div>

                            <p class="mb-0 text-muted">
                                {{ $item->deskripsi }}
                            </p>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div
                class="d-flex justify-content-center align-items-center"
                style="height:300px;"
            >
                <h4 class="text-muted fw-normal">
                    Belum ada jadwal untuk event ini.
                </h4>
            </div>

        @endif

    </div>

</div>

@endsection