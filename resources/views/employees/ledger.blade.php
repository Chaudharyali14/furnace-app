@extends('layouts.app')

@section('title', __('employees.employee_ledger'))

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('employees.employee_ledger') }}</h1>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('employees.back_to_employees') }}</a>
    </div>

    <!-- Session Messages -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Employee Selection Form -->
    <div class="card mb-4">
        <div class="card-header">{{ __('employees.select_employee') }}</div>
        <div class="card-body">
            <form method="GET" action="{{ route('employee.ledger') }}">
                <div class="row">
                    <div class="col-lg-8 col-md-12 mb-3">
                        <select name="employee_id" class="form-select" required>
                            <option value="">{{ __('employees.select_an_employee') }}</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ ($selectedEmployee && $selectedEmployee->id == $employee->id) ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <button type="submit" class="btn btn-primary w-100">{{ __('employees.show_ledger') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedEmployee)
        <!-- Ledger Details -->
        <div class="d-flex justify-content-end mb-3">
            <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('employees.print') }}</button>
            <button id="excelBtn" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> {{ __('employees.excel') }}</button>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>{{ __('employees.ledger_for') }}{{ $selectedEmployee->name }}</h4>
            </div>
            <div class="card-body">
                <h5 class="mb-3">{{ __('employees.transaction_history') }}</h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="employee-ledger-table">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('employees.date') }}</th>
                                <th>{{ __('employees.description') }}</th>
                                <th>{{ __('employees.debit') }}</th>
                                <th>{{ __('employees.credit') }}</th>
                                <th>{{ __('employees.balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d-M-Y') }}</td>
                                    <td>{{ $transaction['description'] }}</td>
                                    <td>{{ $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '' }}</td>
                                    <td>{{ $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '' }}</td>
                                    <td>{{ number_format($transaction['balance'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('employees.no_transactions_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="employee-cards">
                    @foreach ($transactions as $transaction)
                        <div class="employee-card">
                            <p><strong>{{ __('employees.date') }}:</strong> {{ \Carbon\Carbon::parse($transaction['date'])->format('d-M-Y') }}</p>
                            <p><strong>{{ __('employees.description') }}:</strong> {{ $transaction['description'] }}</p>
                            <p><strong>{{ __('employees.debit') }}:</strong> {{ $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '' }}</p>
                            <p><strong>{{ __('employees.credit') }}:</strong> {{ $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '' }}</p>
                            <p><strong>{{ __('employees.balance') }}:</strong> {{ number_format($transaction['balance'], 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Add Payment Form -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>{{ __('employees.add_new_payment') }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('employee.ledger.payment') }}">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <label for="payment_amount" class="form-label">{{ __('employees.payment_amount') }}</label>
                            <input type="number" step="0.01" class="form-control @error('payment_amount') is-invalid @enderror" id="payment_amount" name="payment_amount" required>
                            @error('payment_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <label for="payment_type" class="form-label">{{ __('employees.payment_type') }}</label>
                            <select class="form-select @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                                <option value="credit">{{ __('messages.credit') }}</option>
                                <option value="debit">{{ __('messages.debit') }}</option>
                            </select>
                            @error('payment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <label for="payment_date" class="form-label">{{ __('employees.payment_date') }}</label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label for="description" class="form-label">{{ __('employees.description') }}</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">{{ __('employees.add_payment') }}</button>
                </form>
            </div>
        </div>

        <!-- Add Salary Form -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>{{ __('employees.add_monthly_salary') }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('employee.ledger.salary') }}">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="salary_amount" class="form-label">{{ __('employees.salary_amount') }}</label>
                            <input type="number" step="0.01" class="form-control @error('salary_amount') is-invalid @enderror" id="salary_amount" name="salary_amount" required>
                            @error('salary_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="salary_month" class="form-label">{{ __('employees.salary_month') }}</label>
                            <input type="month" class="form-control @error('salary_month') is-invalid @enderror" id="salary_month" name="salary_month" value="{{ date('Y-m') }}" required>
                            @error('salary_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('employees.add_salary') }}</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const printBtn = document.getElementById('printBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function () {
                const table = document.getElementById('employee-ledger-table').outerHTML;
                const newWin = window.open('');
                newWin.document.write('<html><head><title>Print</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>' + table + '</body></html>');
                newWin.document.close();
                newWin.print();
            });
        }

        const excelBtn = document.getElementById('excelBtn');
        if (excelBtn) {
            excelBtn.addEventListener('click', function () {
                const table = document.getElementById('employee-ledger-table');
                const wb = XLSX.utils.table_to_book(table, {sheet: "{{ __('employees.employee_ledger') }}"});
                XLSX.writeFile(wb, "{{ __('employees.employee_ledger_excel') }}");
            });
        }
    });
</script>
@endpush
