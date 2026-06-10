@extends('layouts.vendor')

@section('content')

<div class="p-8">

    <div class="flex justify-between items-start mb-8">

        <div>
            <h1 class="text-4xl font-bold text-slate-900">
                Jadwal Event
            </h1>

            <p class="text-slate-500 mt-2 text-lg">
                Pantau progress event yang Anda kerjakan (mode hanya-lihat).
            </p>
        </div>

        <div>
            <select
                class="border-2 border-teal-500 rounded-xl px-5 py-3 w-96 text-lg">

                <option>
                    Belum ada event
                </option>

            </select>
        </div>

    </div>

    <div
        class="bg-white rounded-3xl border border-gray-200 h-80 flex items-center justify-center">

        <p class="text-4xl text-slate-500">
            Anda belum ditugaskan ke event apapun.
        </p>

    </div>

</div>

@endsection