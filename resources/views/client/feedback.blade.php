@extends('layouts.client')

@section('title', 'Feedback Event')
@section('page-title', 'Feedback Event')

@section('content')
<div class="card" style="padding:32px;">

    {{-- Header --}}
    <div class="feedback-header">
        <div>
            <h2 style="font-size:32px;font-weight:700;color:#0f172a;margin-bottom:6px;">
                Berikan Feedback Event
            </h2>
            <p style="color:#64748b;font-size:15px;">
                Penilaian Anda membantu kami meningkatkan kualitas layanan event di masa mendatang.
            </p>
        </div>
        <div>
            <span class="status-badge">
                <i class="bi bi-check-circle-fill"></i>
                Event Selesai
            </span>
        </div>
    </div>

    {{-- Informasi Event --}}
    <div class="event-info-box">
        <div class="event-icon">
            <i class="bi bi-calendar-event"></i>
        </div>
        <div>
            <h4>{{ $event->nama_event }}</h4>
            <p>
                <i class="bi bi-calendar3"></i>
                {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}
            </p>
            <p>
                <i class="bi bi-geo-alt-fill"></i>
                {{ $event->lokasi_event }}
            </p>
        </div>
    </div>

    {{-- Alert --}}
    <div class="feedback-alert">
        <i class="bi bi-chat-heart-fill"></i>
        <div>
            Masukan Anda sangat berharga untuk membantu tim Alpha Corp memberikan pelayanan yang lebih baik.
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('feedback.store') }}" method="POST">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">

        {{-- Rating --}}
        <div class="form-group">
            <label class="form-label">Rating Event</label>
            <div class="rating-grid">
                <label class="rating-card">
                    <input type="radio" name="rating" value="5" checked>
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <small>Sangat Puas</small>
                </label>
                <label class="rating-card">
                    <input type="radio" name="rating" value="4">
                    <div class="stars">⭐⭐⭐⭐</div>
                    <small>Puas</small>
                </label>
                <label class="rating-card">
                    <input type="radio" name="rating" value="3">
                    <div class="stars">⭐⭐⭐</div>
                    <small>Cukup</small>
                </label>
                <label class="rating-card">
                    <input type="radio" name="rating" value="2">
                    <div class="stars">⭐⭐</div>
                    <small>Kurang</small>
                </label>
                <label class="rating-card">
                    <input type="radio" name="rating" value="1">
                    <div class="stars">⭐</div>
                    <small>Buruk</small>
                </label>
            </div>
        </div>

        {{-- Ulasan --}}
        <div class="form-group">
            <label class="form-label">Ulasan</label>
            <textarea name="ulasan"
                      rows="6"
                      class="feedback-textarea"
                      placeholder="Tuliskan pengalaman Anda selama menggunakan layanan kami..."></textarea>
        </div>

        {{-- Tombol --}}
        <div style="margin-top:24px;">
            <button type="submit" class="btn-feedback">
                <i class="bi bi-send"></i>
                Kirim Feedback
            </button>
        </div>
    </form>
</div>

{{-- Style --}}
<style>
.feedback-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.status-badge {
    background: #dcfce7;
    color: #15803d;
    padding: 10px 16px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
}
.event-info-box {
    display: flex;
    gap: 18px;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 24px;
}
.event-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background: #14b8a6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.event-info-box h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
}
.event-info-box p {
    margin: 4px 0;
    color: #64748b;
}
.feedback-alert {
    display: flex;
    gap: 12px;
    align-items: center;
    background: #ecfeff;
    border-left: 4px solid #14b8a6;
    padding: 16px;
    border-radius: 10px;
    margin-bottom: 24px;
    color: #0f172a;
}
.feedback-alert i {
    color: #14b8a6;
    font-size: 20px;
}
.form-group {
    margin-bottom: 24px;
}
.form-label {
    display: block;
    font-weight: 700;
    margin-bottom: 12px;
    color: #0f172a;
}
.rating-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}
.rating-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: .2s;
    background: white;
    position: relative;
}
.rating-card:hover {
    border-color: #14b8a6;
    transform: translateY(-2px);
}
.rating-card input {
    position: absolute;
    opacity: 0;
}
.rating-card .stars {
    font-size: 20px;
    margin-bottom: 8px;
}
/* Saat dipilih */
.rating-card:has(input:checked) {
    border: 2px solid #14b8a6;
    background: #ecfeff;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, .15);
}
.feedback-textarea {
    width: 100%;
    padding: 16px;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    resize: none;
    font-size: 15px;
}
.feedback-textarea:focus {
    outline: none;
    border-color: #14b8a6;
}
.btn-feedback {
    background: #14b8a6;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s;
}
.btn-feedback:hover {
    background: #0f9f93;
}
</style>
@endsection
