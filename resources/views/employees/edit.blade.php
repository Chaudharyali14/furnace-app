@extends('layouts.app')

@section('title', __('employees.edit_employee'))

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-edit"></i> {{ __('employees.edit_employee') }}</h3>
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

            <form action="{{ route('employees.update', $employee) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="name" class="form-label">{{ __('employees.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="phone" class="form-label">{{ __('employees.phone') }}</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 mb-3">
                        <label for="address" class="form-label">{{ __('employees.address') }}</label>
                        <textarea class="form-control" id="address" name="address">{{ old('address', $employee->address) }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="joining_date" class="form-label">{{ __('employees.joining_date') }}</label>
                        <input type="date" class="form-control" id="joining_date" name="joining_date" value="{{ old('joining_date', $employee->joining_date) }}">
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="status" class="form-label">{{ __('employees.status') }}</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>{{ __('employees.active') }}</option>
                            <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>{{ __('employees.inactive') }}</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('employees.update_employee') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
