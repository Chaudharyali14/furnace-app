@extends('layouts.app')

@section('title', __('messages.electricity_log'))

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ __('messages.electricity_log') }}</h1>

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

    <!-- Add New Log Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.add_new_electricity_log') }}</h6>
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
            <form action="{{ route('electricity.add_log') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <label for="furnace_id" class="form-label">{{ __('messages.furnace_id') }}</label>
                        <input type="number" class="form-control" id="furnace_id" name="furnace_id" required>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <label for="heat_number" class="form-label">{{ __('messages.heat_no') }}</label>
                        <input type="text" class="form-control" id="heat_number" name="heat_number" required>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <label for="start_time" class="form-label">{{ __('messages.heat_start_time') }}</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <label for="end_time" class="form-label">{{ __('messages.heat_end_time') }}</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <label for="starting_unit" class="form-label">{{ __('messages.starting_unit') }}</label>
                        <input type="number" step="0.01" class="form-control" id="starting_unit" name="starting_unit" required>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <label for="ending_unit" class="form-label">{{ __('messages.ending_unit') }}</label>
                        <input type="number" step="0.01" class="form-control" id="ending_unit" name="ending_unit" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <label for="unit_rate" class="form-label">{{ __('messages.unit_rate') }}</label>
                        <input type="number" step="0.01" class="form-control" id="unit_rate" name="unit_rate" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.add_log') }}</button>
            </form>
        </div>
    </div>

    <!-- Filters and Summary -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.filter_and_summary') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('electricity.log_table') }}" method="GET" class="row g-3">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <label for="filter_type" class="form-label">{{ __('messages.filter_by') }}</label>
                    <select class="form-select" id="filter_type" name="filter_type">
                        <option value="">{{ __('messages.all_time') }}</option>
                        <option value="daily">{{ __('messages.daily') }}</option>
                        <option value="weekly">{{ __('messages.weekly') }}</option>
                        <option value="monthly">{{ __('messages.monthly') }}</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <label for="start_date" class="form-label"><strong>{{ __('messages.start_date_day_to_day') }}:</strong></label>
                    <input type="date" class="form-control" id="start_date" name="start_date">
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <label for="end_date" class="form-label"><strong>{{ __('messages.end_date_day_to_day') }}:</strong></label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-info">{{ __('messages.apply_filter') }}</button>
                </div>
            </form>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>{{ __('messages.total_units_consumed') }}: {{ number_format($totals['total_units'] ?? 0, 2) }}</h5>
                </div>
                <div class="col-md-6">
                    <h5>{{ __('messages.total_cost') }}: {{ number_format($totals['total_cost'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
