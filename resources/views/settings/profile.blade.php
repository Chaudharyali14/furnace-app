@extends('layouts.app')

@section('title', __('messages.profile_settings'))

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm p-4">
        <h1 class="mb-4 text-center fw-bold">{{ __('messages.profile_settings') }}</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('profile.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('messages.email_address') }}</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('messages.update_profile') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
