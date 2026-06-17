@extends('layouts.admin')

@section('title', 'Proposal & Dokumen')
@section('page-title', 'Proposal & Dokumen')

@section('content')
<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>Proposal &amp; Dokumen</h1>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('admin.proposals.index') }}" class="tab-link">Dokumen Umum</a>
    <a href="{{ route('admin.proposals.invoices') }}" class="tab-link">Invoice &amp; Kwitansi</a>
    <a href="{{ route('admin.proposals.builder') }}" class="tab-link active">Proposal Builder</a>
</div>

<div class="tab-content">
    <h2 style="font-size:17px; font-weight:700; color:#0f172a; margin-bottom:18px;">Proposal Builder</h2>

    <form action="{{ route('admin.proposals.generate') }}" method="POST" target="_blank">
        @csrf
        <div class="form-group" style="margin-bottom:20px;">
            <label class="form-label">Pilih Event Target</label>
            <select name="event_id" class="form-input" required>
                <option value="">-- Pilih Event --</option>
                @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom:20px;">
            <label class="form-label">Pilih Bagian Proposal</label>
            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:8px 16px; background:#f8fafc;">
                <div class="form-check">
                    <input type="checkbox" name="sections[]" value="cover" id="cover" checked>
                    <label for="cover">Halaman Sampul (Cover)</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="sections[]" value="profil" id="profil" checked>
                    <label for="profil">Profil Perusahaan</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="sections[]" value="detail_event" id="detail_event" checked>
                    <label for="detail_event">Detail Event &amp; Konsep</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="sections[]" value="rab" id="rab" checked>
                    <label for="rab">Rencana Anggaran Biaya (RAB)</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="sections[]" value="syarat" id="syarat" checked>
                    <label for="syarat">Syarat &amp; Ketentuan</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-file-export"></i> Generate &amp; Print Proposal
        </button>
    </form>
</div>
@endsection