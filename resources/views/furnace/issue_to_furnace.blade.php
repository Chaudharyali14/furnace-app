@extends('layouts.app')

@section('title', __('messages.issued_to_furnace'))

@section('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive {
            display: none;
        }
        .stock-cards {
            display: block;
        }
    }
    @media (min-width: 769px) {
        .stock-cards {
            display: none;
        }
    }
    .stock-card {
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
    <div class="card p-4">
        <h1 class="mb-4">{{ __('messages.issued_to_furnace') }}</h1>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('furnace.issue_to_furnace') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="raw_material_name" class="form-label">{{ __('messages.raw_material') }}</label>
                    <select class="form-select" id="raw_material_name" name="raw_material_name" required>
                        <option value="">{{ __('messages.select_raw_material') }}</option>
                        @if (!empty($raw_materials))
                            @foreach ($raw_materials as $material)
                                <option value="{{ $material->raw_material_name }}">
                                    {{ $material->raw_material_name }} ({{ __('messages.remaining') }}: {{ $material->remaining_stock_qty }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="quantity" class="form-label">{{ __('messages.quantity_to_issue') }}</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0.01" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('messages.issue_to_furnace') }}</button>
        </form>

        <h2 class="mt-5 mb-3">{{ __('messages.raw_material_stock_overview') }}</h2>
        @if (!empty($raw_materials))
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('messages.raw_material') }}</th>
                            <th>{{ __('messages.total_purchased') }}</th>
                            <th>{{ __('messages.total_issued') }}</th>
                            <th>{{ __('messages.remaining_stock') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($raw_materials as $material)
                            <tr>
                                <td>{{ $material->raw_material_name }}</td>
                                <td>{{ $material->total_purchased_qty }}</td>
                                <td>{{ $material->total_issued_qty }}</td>
                                <td>{{ $material->remaining_stock_qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
         
        @else
            <p>{{ __('messages.no_raw_materials_found_in_stock') }}</p>
        @endif
    </div>
</div>
@endsection
