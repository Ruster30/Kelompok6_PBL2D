@extends('layouts.vendor')

@section('content')

<div class="p-8">

    <h1 class="text-4xl font-bold text-slate-900 mb-8">
        Ringkasan
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-6">

                <div class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-10 h-10 text-teal-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>

                    </svg>

                </div>

                <div>
                    <h2 class="text-5xl font-bold">0</h2>
                    <p class="text-2xl text-gray-500">
                        Event Ditugaskan
                    </p>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">

            <div class="flex items-center gap-6">

                <div class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-10 h-10 text-teal-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>

                    </svg>

                </div>

                <div>
                    <h2 class="text-5xl font-bold">0</h2>
                    <p class="text-2xl text-gray-500">
                        Tugas Aktif
                    </p>
                </div>

            </div>

        </div>

        <div class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">

            <div class="flex items-center gap-6">

                <div class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-10 h-10 text-teal-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"/>

                    </svg>

                </div>

                <div>
                    <h2 class="text-5xl font-bold">0</h2>
                    <p class="text-2xl text-gray-500">
                        Tugas Selesai
                    </p>
                </div>

            </div>

        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm">

            <div class="flex justify-between items-center p-8 border-b">

                <h2 class="text-3xl font-bold">
                    Event Terdekat
                </h2>

                <a href="/vendor/event"
                   class="text-teal-600 font-semibold text-xl">
                    Lihat Semua
                </a>

            </div>

            <div class="h-52 flex items-center justify-center">

                <p class="text-2xl text-slate-500">
                    Belum ada event ditugaskan
                </p>

            </div>

        </div>

        <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm">

            <div class="p-8 border-b">

                <h2 class="text-3xl font-bold">
                    Tugas Mendatang
                </h2>

            </div>

            <div class="h-52 flex items-center justify-center">

                <p class="text-2xl text-slate-500">
                    Tidak ada tugas aktif
                </p>

            </div>

        </div>

    </div>

</div>

@endsection