@extends('layouts.app')

@section('title', __('messages.edit_electricity_log'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.edit_electricity_log') }}</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Edit Electricity Log Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.edit_log_entry') }}</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('electricity.update_log', $log) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="furnace_id" class="form-label">{{ __('messages.furnace_id') }}</label>
                        <input type="text" class="form-control" id="furnace_id" name="furnace_id" value="{{ old('furnace_id', $log->furnace_id) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="heat_number" class="form-label">{{ __('messages.heat_no') }}</label>
                        <input type="text" class="form-control" id="heat_number" name="heat_number" value="{{ old('heat_number', $log->heat_number) }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">{{ __('messages.start_time') }}</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($log->start_time)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">{{ __('messages.end_time') }}</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($log->end_time)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="starting_unit" class="form-label">{{ __('messages.starting_unit') }}</label>
                        <input type="number" step="0.01" class="form-control" id="starting_unit" name="starting_unit" value="{{ old('starting_unit', $log->starting_unit) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ending_unit" class="form-label">{{ __('messages.ending_unit') }}</label>
                        <input type="number" step="0.01" class="form-control" id="ending_unit" name="ending_unit" value="{{ old('ending_unit', $log->ending_unit) }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="unit_rate" class="form-label">{{ __('messages.unit_rate') }}</label>
                        <input type="number" step="0.01" class="form-control" id="unit_rate" name="unit_rate" value="{{ old('unit_rate', $log->unit_rate) }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.update_log') }}</button>
                <a href="{{ route('electricity.log_table') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
