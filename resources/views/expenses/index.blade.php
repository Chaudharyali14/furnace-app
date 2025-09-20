@extends('layouts.app')

@section('title', __('expenses.manage_expenses'))

@section('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive {
            display: none;
        }
        .expense-cards {
            display: block;
        }
    }
    @media (min-width: 769px) {
        .expense-cards {
            display: none;
        }
    }
    .expense-card {
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
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('expenses.manage_expenses') }}</h1>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">{{ __('expenses.add_expense') }}</a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <!-- Filter and Export Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('expenses.filter_export_options') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('expenses.index') }}" method="get" class="mb-4">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label for="filter_type" class="form-label"><strong>{{ __('expenses.quick_filter') }}</strong></label>
                        <select class="form-select" id="filter_type" name="filter_type">
                            <option value="" {{ !$filter_type ? 'selected' : '' }}>{{ __('expenses.all') }}</option>
                            <option value="daily" {{ $filter_type == 'daily' ? 'selected' : '' }}>{{ __('expenses.daily') }}</option>
                            <option value="weekly" {{ $filter_type == 'weekly' ? 'selected' : '' }}>{{ __('expenses.weekly') }}</option>
                            <option value="monthly" {{ $filter_type == 'monthly' ? 'selected' : '' }}>{{ __('expenses.monthly') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label for="start_date" class="form-label"><strong>{{ __('expenses.start_date') }}</strong></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $start_date ?? '' }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label for="end_date" class="form-label"><strong>{{ __('expenses.end_date') }}</strong></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $end_date ?? '' }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label for="search" class="form-label"><strong>{{ __('expenses.search') }}</strong></label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> {{ __('expenses.apply_filter') }}</button>
                    </div>
                </div>
            </form>
            <hr>
            <div class="d-flex flex-wrap justify-content-end gap-2">
                <button class="btn btn-info" onclick="printTable()"><i class="fas fa-print"></i> {{ __('expenses.print') }}</button>
                <button class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> {{ __('expenses.export_to_excel') }}</button>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('expenses.expenses_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center" id="expensesTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('expenses.title') }}</th>
                            <th>{{ __('expenses.amount') }}</th>
                            <th>{{ __('expenses.category') }}</th>
                            <th>{{ __('expenses.expense_date') }}</th>
                            <th class="no-print">{{ __('expenses.actions') }}</th> {{-- 🔹 "no-print" class add kiya --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td>{{ $expense->title }}</td>
                                <td>{{ number_format($expense->amount, 2) }}</td>
                                <td>{{ $expense->category }}</td>
                                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</td>
                                <td class="no-print">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-info btn-sm">{{ __('expenses.edit') }}</a>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('expenses.confirm_delete_expense') }}');">{{ __('expenses.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('expenses.no_expenses_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SheetJS for Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    // ✅ Print function (hide "Actions" column)
    function printTable() {
        let table = document.getElementById("expensesTable").cloneNode(true);

        // 🔹 Remove all columns with class "no-print"
        table.querySelectorAll(".no-print").forEach(el => el.remove());

        let newWin = window.open("about:blank", "_blank");
        newWin.document.write(`
            <html>
                <head>
                    <title>{{ __('expenses.print_expenses') }}</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                </head>
                <body>
                    <div class="container mt-4">
                        ${table.outerHTML}
                    </div>
                </body>
            </html>
        `);
        newWin.document.close();
        newWin.focus();
        newWin.print();
    }

    // ✅ Excel Export
    function exportToExcel() {
        let table = document.getElementById("expensesTable").cloneNode(true);
        table.querySelectorAll(".no-print").forEach(el => el.remove()); // 🔹 Excel me bhi "Actions" remove
        let wb = XLSX.utils.table_to_book(table, { sheet: "{{ __('expenses.expenses_sheet') }}" });
        XLSX.writeFile(wb, "{{ __('expenses.expenses_excel_file') }}");
    }
</script>
@endpush
