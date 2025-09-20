@extends('layouts.app')

@section('title', __('messages.electricity_log_table'))

@section('styles')
<style>
    /* Responsive Card Layout for Mobile */
    @media (max-width: 768px) {
        #electricityLogTable {
            display: none;
        }

        .log-cards {
            display: block;
        }

        .log-card {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 1rem;
            padding: 1rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .log-card .card-header {
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .log-card .card-body p {
            margin-bottom: 0.5rem;
        }
    }

    @media (min-width: 769px) {
        .log-cards {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.electricity_log_table') }}</h1>
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

    <!-- Filter and Export Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.filter_export_options') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('electricity.log_table') }}" method="get" class="mb-4">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <label for="filter_type" class="form-label"><strong>{{ __('messages.quick_filter') }}:</strong></label>
                        <select class="form-select" id="filter_type" name="filter_type">
                            <option value="" {{ !$filter_type ? 'selected' : '' }}>{{ __('messages.all') }}</option>
                            <option value="daily" {{ $filter_type == 'daily' ? 'selected' : '' }}>{{ __('messages.daily') }}</option>
                            <option value="weekly" {{ $filter_type == 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                            <option value="monthly" {{ $filter_type == 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <label for="start_date" class="form-label"><strong>{{ __('messages.start_date') }}:</strong></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $start_date ?? '' }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <label for="end_date" class="form-label"><strong>{{ __('messages.end_date') }}:</strong></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $end_date ?? '' }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> {{ __('messages.apply_filter') }}</button>
                    </div>
                </div>
            </form>
            <hr>
            <div class="d-flex justify-content-end">
                <button class="btn btn-info me-2" onclick="printTable()"><i class="fas fa-print"></i> {{ __('messages.print') }}</button>
                <button class="btn btn-success me-2" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> {{ __('messages.excel') }}</button>
                <button class="btn btn-danger" onclick="exportToPdf()"><i class="fas fa-file-pdf"></i> {{ __('messages.pdf') }}</button>
            </div>
        </div>
    </div>

    <!-- Electricity Log Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.electricity_log_details') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="electricityLogTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.furnace_id') }}</th>
                            <th>{{ __('messages.heat_no') }}</th>
                            <th>{{ __('messages.start_time') }}</th>
                            <th>{{ __('messages.end_time') }}</th>
                            <th>{{ __('messages.starting_unit') }}</th>
                            <th>{{ __('messages.ending_unit') }}</th>
                            <th>{{ __('messages.units_consumed') }}</th>
                            <th>{{ __('messages.unit_rate') }}</th>
                            <th>{{ __('messages.total_cost') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($log->log_date)->format('Y-m-d') }}</td>
                                <td>{{ $log->furnace_id }}</td>
                                <td>{{ $log->heat_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->start_time)->format('Y-m-d H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->end_time)->format('Y-m-d H:i') }}</td>
                                <td>{{ number_format($log->starting_unit, 2) }}</td>
                                <td>{{ number_format($log->ending_unit, 2) }}</td>
                                <td>{{ number_format($log->unit_consumed, 2) }}</td>
                                <td>{{ number_format($log->unit_rate, 2) }}</td>
                                <td>{{ number_format($log->total_cost, 2) }}</td>
                                <td>
                                    <a href="{{ route('electricity.edit_log', $log) }}" class="btn btn-info btn-sm">{{ __('messages.edit') }}</a>
                                    <form action="{{ route('electricity.delete_log', $log) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.are_you_sure_you_want_to_delete_this_log_entry') }}');">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">{{ __('messages.no_electricity_logs_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="7" class="text-end"><strong>{{ __('messages.subtotal') }}:</strong></td>
                            <td><strong>{{ number_format($totals['total_units'], 2) }}</strong></td>
                            <td></td>
                            <td><strong>{{ number_format($totals['total_cost'], 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="log-cards">
                @forelse ($logs as $log)
                    <div class="log-card">
                        <div class="card-header">{{ __('messages.heat_no') }}: {{ $log->heat_number }}</div>
                        <div class="card-body">
                            <p><strong>{{ __('messages.date') }}:</strong> {{ \Carbon\Carbon::parse($log->log_date)->format('Y-m-d') }}</p>
                            <p><strong>{{ __('messages.furnace_id') }}:</strong> {{ $log->furnace_id }}</p>
                            <p><strong>{{ __('messages.start_time') }}:</strong> {{ \Carbon\Carbon::parse($log->start_time)->format('Y-m-d H:i') }}</p>
                            <p><strong>{{ __('messages.end_time') }}:</strong> {{ \Carbon\Carbon::parse($log->end_time)->format('Y-m-d H:i') }}</p>
                            <p><strong>{{ __('messages.starting_unit') }}:</strong> {{ number_format($log->starting_unit, 2) }}</p>
                            <p><strong>{{ __('messages.ending_unit') }}:</strong> {{ number_format($log->ending_unit, 2) }}</p>
                            <p><strong>{{ __('messages.units_consumed') }}:</strong> {{ number_format($log->unit_consumed, 2) }}</p>
                            <p><strong>{{ __('messages.unit_rate') }}:</strong> {{ number_format($log->unit_rate, 2) }}</p>
                            <p><strong>{{ __('messages.total_cost') }}:</strong> {{ number_format($log->total_cost, 2) }}</p>
                            <a href="{{ route('electricity.edit_log', $log) }}" class="btn btn-info btn-sm">{{ __('messages.edit') }}</a>
                            <form action="{{ route('electricity.delete_log', $log) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.are_you_sure_you_want_to_delete_this_log_entry') }}');">{{ __('messages.delete') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center">{{ __('messages.no_electricity_logs_found') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printTable() {
        const table = document.getElementById("electricityLogTable").outerHTML;
        const newWin = window.open("");
        newWin.document.write(`
            <html>
                <head>
                    <title>Print Electricity Log</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                </head>
                <body>
                    ${table}
                </body>
            </html>
        `);
        newWin.document.close();
        newWin.print();
    }

    function exportToExcel() {
        const table = document.getElementById("electricityLogTable");
        const wb = XLSX.utils.table_to_book(table, { sheet: "Electricity Log" });
        XLSX.writeFile(wb, "electricity_log.xlsx");
    }

    function exportToPdf() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: "landscape" });
        doc.autoTable({ html: "#electricityLogTable" });
        doc.save("electricity_log.pdf");
    }
</script>
@endpush
