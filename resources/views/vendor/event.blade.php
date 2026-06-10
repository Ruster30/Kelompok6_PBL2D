@extends('layouts.vendor')

@section('content')
<div class="p-8">
    <h1 class="text-3xl font-bold mb-6">Event Saya</h1>

    <div class="bg-white p-6 rounded-xl shadow">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="p-3 text-left">Nama Event</th>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Status</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class="p-3">Wedding Client A</td>
                    <td class="p-3">20 Juni 2026</td>
                    <td class="p-3 text-green-600">Aktif</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection