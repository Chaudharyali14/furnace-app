@extends('layouts.app')

@section('title', __('sales.sales_overview'))

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
    }
</style>
<div class="container-fluid">
    <div class="card shadow-sm p-4">
        <h1 class="mb-4 text-center fw-bold">{{ __('sales.sales_overview') }}</h1>

        @if (session('success'))
            <div class="alert alert-success text-center" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger text-center" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter and Search Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('sales.index') }}" method="get">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <select name="filter" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('sales.all') }}</option>
                                <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>{{ __('sales.daily') }}</option>
                                <option value="weekly" {{ request('filter') == 'weekly' ? 'selected' : '' }}>{{ __('sales.weekly') }}</option>
                                <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>{{ __('sales.monthly') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="{{ __('sales.search') }}" value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary w-100">{{ __('sales.clear') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end mb-3">
            <div class="btn-group">
                <a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{ __('sales.add_sale') }}</a>
                <a href="{{ route('customer.ledger') }}" class="btn btn-secondary"><i class="fas fa-book"></i> {{ __('sales.customer_ledger') }}</a>
                <button onclick="window.printSalesTable()" class="btn btn-info"><i class="fas fa-print"></i> {{ __('sales.print') }}</button>
                <a href="{{ route('sales.export.excel', request()->query()) }}" class="btn btn-success"><i class="fas fa-file-excel"></i> {{ __('sales.excel') }}</a>
                <a href="{{ route('sales.export.pdf', request()->query()) }}" class="btn btn-danger"><i class="fas fa-file-pdf"></i> {{ __('sales.pdf') }}</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="sales-table">
                <thead class="table-dark">
                    <tr>
                        <th>{{ __('sales.id') }}</th>
                        <th>{{ __('sales.sale_date') }}</th>
                        <th>{{ __('sales.customer') }}</th>
                        <th>{{ __('sales.item') }}</th>
                        <th>{{ __('sales.billet_size') }}</th>
                        <th>{{ __('sales.total_weight') }}</th>
                        <th>{{ __('sales.rate') }}</th>
                        <th>{{ __('sales.sub_total') }}</th>
                        <th>{{ __('sales.discount') }}</th>
                        <th>{{ __('sales.paid_amount') }}</th>
                        <th>{{ __('sales.remaining_amount') }}</th>
                        <th class="text-center">{{ __('sales.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-M-Y') }}</td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $sale->item_name ?? 'N/A' }}</td>
                            <td>{{ $sale->billet_size ?? 'N/A' }}</td>
                            <td>{{ number_format($sale->total_weight, 2) }}</td>
                            <td>{{ number_format($sale->rate, 2) }}</td>
                            <td>{{ number_format($sale->sub_total, 2) }}</td>
                            <td>{{ number_format($sale->discount, 2) }}</td>
                            <td>{{ number_format($sale->paid_amount, 2) }}</td>
                            <td class="text-danger">{{ number_format($sale->remaining_amount, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('sales.destroy', $sale->id) }}" method="post" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('sales.confirm_delete') }}')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">{{ __('sales.no_sales_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-dark fw-bold">
                        <td colspan="5" class="text-end">{{ __('sales.total') }}</td>
                        <td>{{ number_format($sales->sum('total_weight'), 2) }}</td>
                        <td></td>
                        <td>{{ number_format($sales->sum('sub_total'), 2) }}</td>
                        <td>{{ number_format($sales->sum('discount'), 2) }}</td>
                        <td>{{ number_format($sales->sum('paid_amount'), 2) }}</td>
                        <td class="text-danger">{{ number_format($sales->sum('remaining_amount'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printSalesTable() {
        var printContents = document.getElementById('sales-table').outerHTML;
        var originalContents = document.body.innerHTML;
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>{{ __('sales.print_sales_table') }}</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">');
        printWindow.document.write('<style>body{font-family: sans-serif;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h1 class="text-center">{{ __('sales.sales_details') }}</h1>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 250);
    }
</script>
@endpush