@extends('layouts.admin')

@section('title', 'Penugasan')
@section('page-title', 'Penugasan')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h1>Penugasan</h1>
        <p>Kelola penugasan vendor untuk setiap event.</p>
    </div>

    <button class="btn btn-primary"
        onclick="document.getElementById('vendorModal').classList.add('show')">
        <i class="fas fa-plus"></i>
        Tambah Penugasan
    </button>
</div>

<div class="card">

    <div class="card-header">
        <div class="toolbar">

            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari event..."
                    value="{{ request('search') }}">
            </div>

            <select id="statusFilter" class="select-filter">
                <option value="">Semua Status</option>

                <option value="ditugaskan"
                    {{ request('status')=='ditugaskan' ? 'selected' : '' }}>
                    Ditugaskan
                </option>

                <option value="dikerjakan"
                    {{ request('status')=='dikerjakan' ? 'selected' : '' }}>
                    Dikerjakan
                </option>

                <option value="selesai"
                    {{ request('status')=='selesai' ? 'selected' : '' }}>
                    Selesai
                </option>

            </select>

        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Vendor</th>
                <th>Jadwal</th>
                <th>Status</th>
                <th>Harga Vendor</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($eventVendors as $item)

            <tr>

                <td>
                    {{ $item->event->nama_event ?? '-' }}
                </td>

                <td>
                    {{ $item->vendor->nama_vendor ?? '-' }}
                </td>

                <td>
                    {{ $item->jadwal_vendor
                        ? \Carbon\Carbon::parse($item->jadwal_vendor)->format('d M Y')
                        : '-' }}
                </td>

                <td>

                    @php

                    $badge = match($item->status_vendor){

                        'ditugaskan' => 'badge-pending',
                        'dikerjakan' => 'badge-active',
                        'selesai' => 'badge-succes',

                        default => 'badge-gray'
                    };

                    @endphp

                    <span class="badge {{ $badge }}">
                        {{ ucfirst($item->status_vendor) }}
                    </span>

                </td>

                <td>
                    Rp {{ number_format($item->harga_vendor,0,',','.') }}
                </td>

                <td>

                    <div class="action-btns">

                        <button
                            class="action-btn"
                            onclick='editData(@json($item))'>

                            <i class="fas fa-edit"></i>

                        </button>

                        <form
                            action="{{ route('admin.event-vendors.destroy',$item->id) }}"
                            method="POST"
                            style="display:inline"
                            onsubmit="return swalDelete(this, {text: 'Penugasan vendor {{ addslashes($item->vendor->nama_vendor ?? '') }} akan dihapus.'})">

                            @csrf
                            @method('DELETE')

                            <button
                                class="action-btn danger">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6" class="text-center">
                    Belum ada penugasan vendor
                </td>
            </tr>

        @endforelse

        </tbody>
    </table>

    <div style="padding:20px">
        {{ $eventVendors->links() }}
    </div>

</div>

{{-- MODAL --}}

<div id="vendorModal" class="modal-overlay">

    <div class="modal-box">

        <div class="modal-header">

            <span id="modalTitle">
                Tambah Penugasan
            </span>

            <button
                class="modal-close"
                onclick="document.getElementById('vendorModal').classList.remove('show')">

                <i class="fas fa-times"></i>

            </button>

        </div>

        <form
            id="vendorForm"
            action="{{ route('admin.event-vendors.store') }}"
            method="POST">

            @csrf

            <input
                type="hidden"
                id="formMethod"
                name="_method"
                value="POST">

            <div class="modal-body">

                <div class="form-group">

                    <label>Event</label>

                    <select
                        name="event_id"
                        id="event_id"
                        class="form-input"
                        required>

                        <option value="">
                            Pilih Event
                        </option>

                        @foreach($events as $event)

                        <option value="{{ $event->id }}">
                            {{ $event->nama_event }}
                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Vendor</label>

                    <select
                        name="vendor_id"
                        id="vendor_id"
                        class="form-input"
                        required>

                        <option value="">
                            Pilih Vendor
                        </option>

                        @foreach($vendors as $vendor)

                        <option value="{{ $vendor->id }}">
                            {{ $vendor->nama_vendor }}
                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Jadwal Vendor</label>

                    <input
                        type="date"
                        id="jadwal_vendor"
                        name="jadwal_vendor"
                        class="form-input">

                </div>

                <div class="form-group">

                    <label>Status</label>

                    <select
                        name="status_vendor"
                        id="status_vendor"
                        class="form-input">

                        <option value="ditugaskan">
                            Ditugaskan
                        </option>

                        <option value="dikerjakan">
                            Dikerjakan
                        </option>

                        <option value="selesai">
                            Selesai
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Harga Vendor</label>

                    <input
                        type="number"
                        name="harga_vendor"
                        id="harga_vendor"
                        class="form-input">

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline"
                    onclick="document.getElementById('vendorModal').classList.remove('show')">

                    Batal

                </button>

                <button
                    type="submit"
                    class="btn btn-primary">

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')

<script>

function editData(data)
{
    document.getElementById('modalTitle').innerText='Edit Penugasan';

    document.getElementById('event_id').value=data.event_id;
    document.getElementById('vendor_id').value=data.vendor_id;
    document.getElementById('jadwal_vendor').value=data.jadwal_vendor;
    document.getElementById('status_vendor').value=data.status_vendor;
    document.getElementById('harga_vendor').value=data.harga_vendor;

    document.getElementById('vendorForm').action =
        '/admin/event-vendors/'+data.id;

    document.getElementById('formMethod').value='PUT';

    document.getElementById('vendorModal').classList.add('show');
}

</script>

@endpush