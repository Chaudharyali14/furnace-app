@extends('layouts.app')

@section('title', __('reports.profit_loss_report'))

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm p-4">
        <h1 class="mb-4 text-center fw-bold">{{ __('reports.profit_loss_report') }}</h1>

        <!-- Filter Form -->
        <form action="{{ route('reports.profit_loss') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="filter">{{ __('reports.filter_by') }}</label>
                    <select name="filter" id="filter" class="form-select" onchange="handleFilterChange(this)">
                        <option value="">{{ __('reports.select_filter') }}</option>
                        <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>{{ __('reports.daily') }}</option>
                        <option value="weekly" {{ request('filter') == 'weekly' ? 'selected' : '' }}>{{ __('reports.weekly') }}</option>
                        <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>{{ __('reports.monthly') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="start_date">{{ __('reports.start_date') }}</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date">{{ __('reports.end_date') }}</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('reports.filter') }}</button>
                    <a href="{{ route('reports.profit_loss') }}" class="btn btn-secondary ms-2">{{ __('reports.clear') }}</a>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-end mb-3">
            <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('reports.print') }}</button>
            <button id="excelBtn" class="btn btn-success"><i class="fas fa-file-excel"></i> {{ __('reports.excel') }}</button>
        </div>

        <!-- Summary Section -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('reports.total_sales') }}</h5>
                        <p class="card-text fs-4 fw-bold text-success">{{ number_format($totalSales, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('reports.total_purchases') }}</h5>
                        <p class="card-text fs-4 fw-bold text-danger">{{ number_format($totalPurchases, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('reports.total_salaries') }}</h5>
                        <p class="card-text fs-4 fw-bold text-danger">{{ number_format($totalSalaries, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('reports.total_electricity') }}</h5>
                        <p class="card-text fs-4 fw-bold text-danger">{{ number_format($totalElectricity, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('reports.total_expenses') }}</h5>
                        <p class="card-text fs-4 fw-bold text-danger">{{ number_format($totalExpenses, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('reports.profit_loss') }}</h5>
                        <p class="card-text fs-4 fw-bold {{ $profitLoss >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($profitLoss, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="profit-loss-report-table">
                <thead class="table-dark">
                    <tr>
                        <th>{{ __('reports.date') }}</th>
                        <th>{{ __('reports.description') }}</th>
                        <th>{{ __('reports.debit') }}</th>
                        <th>{{ __('reports.credit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="text-center table-secondary"><strong>{{ __('reports.sales') }}</strong></td>
                    </tr>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-M-Y') }}</td>
                            <td>{{ $sale->item_name }} {{ __('reports.sold_to') }} {{ $sale->customer->name }}</td>
                            <td></td>
                            <td>{{ number_format($sale->sub_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ __('reports.no_sales_found_for_period') }}</td>
                        </tr>
                    @endforelse
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-bold">{{ __('reports.total_sales_table') }}</td>
                        <td class="fw-bold">{{ number_format($totalSales, 2) }}</td>
                    </tr>

                    <tr>
                        <td colspan="4" class="text-center table-secondary"><strong>{{ __('reports.purchases') }}</strong></td>
                    </tr>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-M-Y') }}</td>
                            <td>{{ $purchase->scrap_name }} {{ __('reports.purchased_from') }} {{ $purchase->supplier->name }}</td>
                            <td>{{ number_format($purchase->grand_total, 2) }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ __('reports.no_purchases_found_for_period') }}</td>
                        </tr>
                    @endforelse
                    <tr class="table-light">
                        <td colspan="2" class="text-end fw-bold">{{ __('reports.total_purchases_table') }}</td>
                        <td class="fw-bold">{{ number_format($totalPurchases, 2) }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="4" class="text-center table-secondary"><strong>{{ __('reports.expenses') }}</strong></td>
                    </tr>
                    @forelse ($expenses as $expense)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-M-Y') }}</td>
                            <td>{{ $expense->title }} ({{ $expense->category }})</td>
                            <td>{{ number_format($expense->amount, 2) }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ __('reports.no_expenses_found_for_period') }}</td>
                        </tr>
                    @endforelse
                    <tr class="table-light">
                        <td colspan="2" class="text-end fw-bold">{{ __('reports.total_expenses_table') }}</td>
                        <td class="fw-bold">{{ number_format($totalExpenses, 2) }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="4" class="text-center table-secondary"><strong>{{ __('reports.salaries') }}</strong></td>
                    </tr>
                    @forelse ($salaries as $salary)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($salary->salary_month)->format('d-M-Y') }}</td>
                            <td>{{ __('reports.salary_for') }} {{ $salary->employee->name }}</td>
                            <td>{{ number_format($salary->amount, 2) }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ __('reports.no_salaries_found_for_period') }}</td>
                        </tr>
                    @endforelse
                    <tr class="table-light">
                        <td colspan="2" class="text-end fw-bold">{{ __('reports.total_salaries_table') }}</td>
                        <td class="fw-bold">{{ number_format($totalSalaries, 2) }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="4" class="text-center table-secondary"><strong>{{ __('reports.electricity') }}</strong></td>
                    </tr>
                    @forelse ($electricityLogs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->log_date)->format('d-M-Y') }}</td>
                            <td>{{ __('reports.electricity_log') }}{{ $log->id }}</td>
                            <td>{{ number_format($log->total_cost, 2) }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ __('reports.no_electricity_logs_found_for_period') }}</td>
                        </tr>
                    @endforelse
                    <tr class="table-light">
                        <td colspan="2" class="text-end fw-bold">{{ __('reports.total_electricity_table') }}</td>
                        <td class="fw-bold">{{ number_format($totalElectricity, 2) }}</td>
                        <td></td>
                    </tr>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="2" class="text-end"><h4>{{ __('reports.total_profit_loss') }}</h4></th>
                        <th colspan="2"><h4 class="{{ $profitLoss >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($profitLoss, 2) }}</h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Print
        document.getElementById('printBtn').addEventListener('click', function () {
            const table = document.getElementById('profit-loss-report-table').outerHTML;
            const newWin = window.open('');
            newWin.document.write('<html><head><title>Print</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>' + table + '</body></html>');
            newWin.document.close();
            newWin.print();
        });

        // Excel
        document.getElementById('excelBtn').addEventListener('click', function () {
            const table = document.getElementById('profit-loss-report-table');
            const wb = XLSX.utils.table_to_book(table, {sheet: "{{ __('reports.profit_loss_report_sheet') }}"});
            XLSX.writeFile(wb, "{{ __('reports.profit_loss_report_excel_file') }}");
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
