{{-- resources/views/landing/index.blade.php --}}
@extends('layouts.app')

@section('title', 'ALPHA.COM - Menciptakan Event yang Sempurna')

@section('content')
    @include('landing.hero')
    @include('landing.about')
    @include('landing.visi-misi')
    @include('landing.why-us')
@endsection