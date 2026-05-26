{{-- resources/views/landing/index.blade.php --}}
@extends('layouts.app')

@section('title', 'ALPHA.COM - Menciptakan Event yang Sempurna')

@section('content')
    @include('sections.hero')
    @include('sections.about')
    @include('sections.why-us')
    @include('sections.services')
    @include('sections.team')
    @include('sections.portfolio')
    @include('sections.clients')
    @include('sections.contact')
@endsection