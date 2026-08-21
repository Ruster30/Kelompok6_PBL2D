@php
    $profileUser = auth()->user();
    $layout = match (true) {
        $profileUser?->isDirector()  => 'layouts.director',
        $profileUser?->isVendor()    => 'layouts.vendor',
        $profileUser?->isClient()    => 'layouts.client',
        default                      => 'layouts.admin',
    };
@endphp

@extends($layout)

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
@endpush