@extends('layouts.app')

@section('title', __('messages.application_settings'))

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm p-4">
        <h1 class="mb-4 text-center fw-bold">{{ __('messages.application_settings') }}</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('application.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="theme" class="form-label">{{ __('messages.theme') }}</label>
                        <select class="form-select" id="theme" name="theme">
                            <option value="light" {{ auth()->user()->theme == 'light' ? 'selected' : '' }}>{{ __('messages.light') }}</option>
                            <option value="dark" {{ auth()->user()->theme == 'dark' ? 'selected' : '' }}>{{ __('messages.dark') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="language" class="form-label">{{ __('messages.language') }}</label>
                        <select class="form-select" id="language" name="language">
                            <option value="en" {{ auth()->user()->language == 'en' ? 'selected' : '' }}>{{ __('messages.english') }}</option>
                            <option value="es" {{ auth()->user()->language == 'es' ? 'selected' : '' }}>{{ __('messages.spanish') }}</option>
                            <option value="ur" {{ auth()->user()->language == 'ur' ? 'selected' : '' }}>{{ __('messages.urdu') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_settings') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
