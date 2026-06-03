{{-- resources/views/landing/index.blade.php --}}
@extends('layouts.landing')

@section('title', 'ALPHA.COM - Menciptakan Event yang Sempurna')

@section('content')
    @include('landing.hero')
    @include('landing.about')
    @include('landing.visi-misi')
    @include('landing.why-us')
    @include('landing.services')
    @include('landing.team')
    @include('landing.portofolio')
    @include('landing.clients')
@endsection