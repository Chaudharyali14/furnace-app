@extends('layouts.app')

@section('title', __('employees.employees'))

@section('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive {
            display: none;
        }
        .employee-cards {
            display: block;
        }
    }
    @media (min-width: 769px) {
        .employee-cards {
            display: none;
        }
    }
    .employee-card {
        background-color: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        margin-bottom: 1rem;
        padding: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> {{ __('employees.employees') }}</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <a href="{{ route('employees.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> {{ __('employees.add_employee') }}</a>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('employees.name') }}</th>
                            <th>{{ __('employees.phone') }}</th>
                            <th>{{ __('employees.address') }}</th>
                            <th>{{ __('employees.joining_date') }}</th>
                            <th>{{ __('employees.status') }}</th>
                            <th>{{ __('employees.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->phone }}</td>
                                <td>{{ $employee->address }}</td>
                                <td>{{ $employee->joining_date }}</td>
                                <td>{{ $employee->status }}</td>
                                <td>
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __("employees.confirm_delete") }}');"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

         
        </div>
    </div>
</div>
@endsection
