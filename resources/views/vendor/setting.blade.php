@extends('layouts.vendor')

@section('content')
<div class="p-8">
    <h1 class="text-3xl font-bold mb-6">Pengaturan</h1>

    <div class="bg-white p-6 rounded-xl shadow">
        <form>

            <div class="mb-4">
                <label class="block mb-2">Nama Vendor</label>

                <input
                    type="text"
                    class="border p-3 rounded w-full"
                    value="Restia Vendor">
            </div>

            <div class="mb-4">
                <label class="block mb-2">Email</label>

                <input
                    type="email"
                    class="border p-3 rounded w-full"
                    value="vendor@mail.com">
            </div>

            <button
                class="bg-teal-500 text-white px-5 py-2 rounded">
                Simpan
            </button>

        </form>
    </div>
</div>
@endsection