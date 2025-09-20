@extends('layouts.app')

@section('title', __('sales.sales_report'))

@section('content')
<style>
    @media (max-width: 767.98px) {
        .btn-group {
            flex-direction: column;
            width: 100%;
        }
        .btn-group .btn {
            margin-bottom: 5px;
            width: 100%;
        }
        .input-group {
            flex-wrap: wrap;
        }
        .input-group .form-control,
        .input-group .btn {
            flex: 1 1 auto;
            width: 100%;
        }
        .input-group .form-control:first-child {
            border-top-right-radius: var(--bs-border-radius);
            border-bottom-right-radius: 0;
        }
        .input-group .form-control:last-child {
            border-top-left-radius: 0;
            border-bottom-left-radius: var(--bs-border-radius);
        }
        .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .input-group:not(.has-validation) > :not(:last-child):not(.dropdown-toggle):not(.dropdown-menu):not(.form-floating),
        .input-group:not(.has-validation) > .dropdown-toggle:nth-last-child(n+3),
        .input-group:not(.has-validation) > .form-floating:not(:last-child) > .form-control,
        .input-group:not(.has-validation) > .form-floating:not(:last-child) > .form-select {
            border-top-right-radius: var(--bs-border-radius);
            border-bottom-right-radius: 0;
        }
        .input-group > :not(:first-child):not(.dropdown-menu):not(.form-floating):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
            margin-left: 0;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-control {
            margin-bottom: 10px;
        }
        .d-flex.justify-content-end.mb-3 {
            flex-direction: column;
            align-items: stretch;
        }
        .d-flex.justify-content-end.mb-3 .btn {
            margin-bottom: 5px;
            width: 100%;
        }
        .d-flex.justify-content-end.mb-3 .btn.me-2 {
            margin-right: 0 !important;
        }
        .card.text-center.h-100 {
            margin-bottom: 15px;
        }
    }
</style>
<div class="container-fluid">
    <div class="card shadow-sm p-4">
        <h1 class="mb-4 text-center fw-bold">{{ __('sales.sales_report') }}</h1>

        <!-- Filter Form -->
        <form action="{{ route('sales.report') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="filter">{{ __('sales.filter_by') }}</label>
                    <select name="filter" id="filter" class="form-select" onchange="handleFilterChange(this)">
                        <option value="">{{ __('sales.select_filter') }}</option>
                        <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>{{ __('sales.daily') }}</option>
                        <option value="weekly" {{ request('filter') == 'weekly' ? 'selected' : '' }}>{{ __('sales.weekly') }}</option>
                        <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>{{ __('sales.monthly') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date">{{ __('sales.start_date') }}</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('sales.end_date') }}</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="search">{{ __('sales.search') }}</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="{{ __('sales.search_by_item_or_customer') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('sales.filter') }}</button>
                    <a href="{{ route('sales.report') }}" class="btn btn-secondary ms-2">{{ __('sales.clear') }}</a>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-end mb-3">
            <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('sales.print') }}</button>
            <button id="excelBtn" class="btn btn-success"><i class="fas fa-file-excel"></i> {{ __('sales.excel') }}</button>
        </div>

        <!-- Summary Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('sales.total_sales_amount') }}</h5>
                        <p class="card-text fs-4 fw-bold">{{ number_format($totalAmount, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('sales.total_weight_kg') }}</h5>
                        <p class="card-text fs-4 fw-bold">{{ number_format($totalWeight, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('sales.unique_items_sold') }}</h5>
                        <p class="card-text fs-4 fw-bold">{{ $uniqueItemCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="sales-report-table">
                <thead class="table-dark">
                    <tr>
                        <th>{{ __('sales.sale_date') }}</th>
                        <th>{{ __('sales.customer') }}</th>
                        <th>{{ __('sales.item_name') }}</th>
                        <th>{{ __('sales.weight_kg') }}</th>
                        <th>{{ __('sales.rate') }}</th>
                        <th>{{ __('sales.total_amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-M-Y') }}</td>
                            <td>{{ $sale->customer->name }}</td>
                            <td>{{ $sale->item_name }}</td>
                            <td>{{ number_format($sale->total_weight, 2) }}</td>
                            <td>{{ number_format($sale->rate, 2) }}</td>
                            <td>{{ number_format($sale->sub_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('sales.no_sales_found_for_period') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Print
        document.getElementById('printBtn').addEventListener('click', function () {
            const table = document.getElementById('sales-report-table').outerHTML;
            const newWin = window.open('');
            newWin.document.write('<html><head><title>Print</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>' + table + '</body></html>');
            newWin.document.close();
            newWin.print();
        });

        // Excel
        document.getElementById('excelBtn').addEventListener('click', function () {
            const table = document.getElementById('sales-report-table');
            const wb = XLSX.utils.table_to_book(table, {sheet: "{{ __('sales.sales_report') }}"});
            XLSX.writeFile(wb, "{{ __('sales.sales_report_excel') }}");
        });
    });

    function handleFilterChange(select) {
        if (select.value) {
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
        }
        select.form.submit();
    }
</script>
@endpush
