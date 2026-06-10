@extends('layouts.vendor')

@section('content')

<div class="p-8">

    <div class="mb-8">

        <h1 class="text-4xl font-bold text-slate-900">
            Daftar Tugas
        </h1>

        <p class="text-slate-500 text-xl mt-2">
            Kelola dan perbarui progres tugas Anda
        </p>

    </div>

    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden">

        <table class="w-full">

            <thead class="border-b bg-gray-50">

                <tr>

                    <th class="text-left px-6 py-6 text-2xl font-semibold">
                        Tugas
                    </th>

                    <th class="text-left px-6 py-6 text-2xl font-semibold">
                        Event
                    </th>

                    <th class="text-left px-6 py-6 text-2xl font-semibold">
                        Deadline
                    </th>

                    <th class="text-left px-6 py-6 text-2xl font-semibold">
                        Progres
                    </th>

                    <th class="text-left px-6 py-6 text-2xl font-semibold">
                        Status
                    </th>

                    <th class="text-left px-6 py-6 text-2xl font-semibold">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td colspan="6"
                        class="text-center py-16 text-3xl text-slate-500">

                        Belum ada tugas yang diberikan

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection