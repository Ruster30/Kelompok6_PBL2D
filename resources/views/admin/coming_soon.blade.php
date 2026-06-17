@extends('layouts.admin')

@section('title', $title)
@section('page-title', $title)

@section('content')
<div class="card">
    <div class="empty-state" style="padding:80px 20px;">
        <i class="fas fa-tools" style="font-size:48px; color:#cbd5e1;"></i>
        <h3 style="font-size:18px;">{{ $title }}</h3>
        <p>Halaman ini sedang dalam pengembangan.</p>
    </div>
</div>
@endsection
