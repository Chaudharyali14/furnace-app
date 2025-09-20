@extends('layouts.app')

@section('title', __('purchase.purchase_report'))

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm p-4">
        <h1 class="mb-4 text-center fw-bold">{{ __('purchase.purchase_report') }}</h1>

        <!-- Filter Form -->
        <form action="{{ route('purchase.report') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <label for="filter">{{ __('purchase.filter_by') }}</label>
                    <select name="filter" id="filter" class="form-select" onchange="handleFilterChange(this)">
                        <option value="">{{ __('purchase.select_filter') }}</option>
                        <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>{{ __('purchase.daily') }}</option>
                        <option value="weekly" {{ request('filter') == 'weekly' ? 'selected' : '' }}>{{ __('purchase.weekly') }}</option>
                        <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>{{ __('purchase.monthly') }}</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <label for="start_date">{{ __('purchase.start_date') }}</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <label for="end_date">{{ __('purchase.end_date') }}</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <label for="search">{{ __('purchase.search') }}</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="{{ __('purchase.search_by_item_or_supplier') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('purchase.filter') }}</button>
                    <a href="{{ route('purchase.report') }}" class="btn btn-secondary ms-2">{{ __('purchase.clear') }}</a>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-end mb-3">
            <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('purchase.print') }}</button>
            <button id="excelBtn" class="btn btn-success"><i class="fas fa-file-excel"></i> {{ __('purchase.excel') }}</button>
        </div>

        <!-- Summary Section -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('purchase.total_purchase_amount') }}</h5>
                        <p class="card-text fs-4 fw-bold">{{ number_format($totalAmount, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('purchase.total_weight_kg') }}</h5>
                        <p class="card-text fs-4 fw-bold">{{ number_format($totalWeight, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('purchase.unique_items_purchased') }}</h5>
                        <p class="card-text fs-4 fw-bold">{{ $uniqueItemCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="purchase-report-table">
                <thead class="table-dark">
                    <tr>
                        <th>{{ __('purchase.purchase_date') }}</th>
                        <th>{{ __('purchase.supplier') }}</th>
                        <th>{{ __('purchase.item_name') }}</th>
                        <th>{{ __('purchase.weight_kg') }}</th>
                        <th>{{ __('purchase.amount_per_kg') }}</th>
                        <th>{{ __('purchase.total_amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-M-Y') }}</td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td>{{ $purchase->scrap_name }}</td>
                            <td>{{ number_format($purchase->weight, 2) }}</td>
                            <td>{{ number_format($purchase->amount_per_kg, 2) }}</td>
                            <td>{{ number_format($purchase->grand_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('purchase.no_purchases_found_for_period') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Print
        document.getElementById('printBtn').addEventListener('click', function () {
            const table = document.getElementById('purchase-report-table').outerHTML;
            const newWin = window.open('');
            newWin.document.write('<html><head><title>Print</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>' + table + '</body></html>');
            newWin.document.close();
            newWin.print();
        });

        // Excel
        document.getElementById('excelBtn').addEventListener('click', function () {
            const table = document.getElementById('purchase-report-table');
            const wb = XLSX.utils.table_to_book(table, {sheet: "{{ __('purchase.purchase_report_sheet') }}"});
            XLSX.writeFile(wb, "{{ __('purchase.purchase_report_excel_file') }}");
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
