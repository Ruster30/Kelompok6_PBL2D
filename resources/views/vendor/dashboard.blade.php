@extends('layouts.vendor')

@section('content')

<div class="p-8">

    <div class="grid grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow p-8">

            <div class="text-5xl font-bold">
                0
            </div>

            <div class="text-gray-500 mt-2">
                Event Ditugaskan
            </div>

        </div>

        <div class="bg-white rounded-xl shadow p-8">

            <div class="text-5xl font-bold">
                0
            </div>

            <div class="text-gray-500 mt-2">
                Tugas Aktif
            </div>

        </div>

        <div class="bg-white rounded-xl shadow p-8">

            <div class="text-5xl font-bold">
                0
            </div>

            <div class="text-gray-500 mt-2">
                Tugas Selesai
            </div>

        </div>

    </div>

    <div class="grid grid-cols-2 gap-6 mt-8">

        <div class="bg-white rounded-xl shadow">

            <div class="p-6 border-b">

                <h3 class="text-2xl font-bold">
                    Event Terdekat
                </h3>

            </div>

            <div class="h-48 flex items-center justify-center text-gray-500">

                Belum ada event ditugaskan

            </div>

        </div>

        <div class="bg-white rounded-xl shadow">

            <div class="p-6 border-b">

                <h3 class="text-2xl font-bold">
                    Tugas Mendatang
                </h3>

            </div>

            <div class="h-48 flex items-center justify-center text-gray-500">

                Tidak ada tugas aktif

            </div>

        </div>

    </div>

</div>

@endsection